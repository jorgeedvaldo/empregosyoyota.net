<?php

namespace App\Models;

use App\Exceptions\DatabaseUnavailableException;
use App\Services\DatabaseFallbackCache;
use App\Services\EmergencySnapshotService;
use App\Services\DatabaseCircuitBreaker;
use App\Support\DatabaseAvailability;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Throwable;

class Article extends Model
{
    use HasFactory;

    /** Dimensoes (px) e qualidade da thumb gerada para os cards de artigo. */
    const THUMB_WIDTH = 640;
    const THUMB_HEIGHT = 400;
    const THUMB_QUALITY = 75;

    protected $fillable = [
        'title', 'slug', 'description', 'photo'
    ];

	protected static function boot()
    {
        parent::boot();

        static::created(function ($article) {
            $article->slug = $article->generateSlug($article->title, $article->id);
            $article->save();
        });

        static::saved(function ($article) {
            $article->generateThumbnail();
            self::syncAfterCommit($article, deleted: false);
        });

        static::deleted(function ($article) {
            self::syncAfterCommit($article, deleted: true);
        });
    }

    /**
     * Caminho (relativo ao disco "public") da thumb da foto do artigo.
     * Segue a mesma estrutura de pastas da foto original, apenas com um
     * prefixo "thumb/": images/articles/x.jpg -> thumb/images/articles/x.jpg
     */
    public function thumbPath(): ?string
    {
        return $this->photo ? 'thumb/' . $this->photo : null;
    }

    /**
     * Gera a thumb otimizada da foto do artigo usando o Intervention
     * Image, caso ainda nao exista. Falha silenciosamente se a imagem
     * de origem nao existir ou nao puder ser processada — a view usa a
     * foto original como fallback nesse caso.
     */
    public function generateThumbnail(bool $force = false): void
    {
        $thumbPath = $this->thumbPath();

        if (!$thumbPath) {
            return;
        }

        if (!$force && Storage::disk('public')->exists($thumbPath)) {
            return;
        }

        $sourcePath = Storage::disk('public')->path($this->photo);

        if (!is_file($sourcePath)) {
            return;
        }

        try {
            $manager = new ImageManager(['driver' => 'gd']);
            $encoded = (string) $manager->make($sourcePath)
                ->fit(self::THUMB_WIDTH, self::THUMB_HEIGHT)
                ->encode(null, self::THUMB_QUALITY);

            Storage::disk('public')->put($thumbPath, $encoded);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * URL publica da thumb, com fallback para a foto original caso a
     * thumb ainda nao tenha sido gerada.
     */
    public function getPhotoThumbUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        $thumbPath = $this->thumbPath();

        return Storage::disk('public')->exists($thumbPath)
            ? asset('storage/' . $thumbPath)
            : asset('storage/' . $this->photo);
    }

    /**
     * Confirma primeiro a gravacao no MySQL (DB::afterCommit), so depois
     * invalida a cache de leitura e sincroniza este UNICO artigo no
     * snapshot de emergencia.
     */
    private static function syncAfterCommit(self $article, bool $deleted): void
    {
        DB::afterCommit(function () use ($article, $deleted) {
            self::clearCache($article);

            $snapshot = app(EmergencySnapshotService::class);

            $deleted
                ? $snapshot->removeArticle($article->id)
                : $snapshot->upsertArticle($article->toSnapshotArray());
        });
    }

    private static function clearCache($article)
    {
        // So a copia normal: a de emergencia sobrevive (ver DatabaseFallbackCache).
        $cache = app(DatabaseFallbackCache::class);
        $cache->forget('latest_articles_50');
        $cache->forget('rss_feed_items');
        $cache->forget('articles_count_country_1');

        Cache::forever('sitemap_version', ((int) Cache::get('sitemap_version', 1)) + 1);
    }

    private function generateSlug($title, $id)
    {
        if (static::whereSlug($slug = Str::slug($title))->exists()) {
            $max = static::whereTitle($title)->latest('id');
            $slug = $slug . '-' . $id;
        }
        return $slug;
    }

    /**
     * O que o snapshot de emergencia guarda deste artigo — so os campos
     * que as vistas publicas usam. "photo_thumb_url" e pre-calculado aqui
     * (em vez de depender do accessor durante a emergencia) para nao
     * depender do disco estar como se espera nesse momento.
     */
    public function toSnapshotArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'photo' => $this->photo,
            'photo_thumb_url' => $this->photo_thumb_url,
            'country_id' => $this->country_id,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }

    /**
     * Total de artigos, para a paginacao de /articles.
     */
    public static function getCachedCount(): int
    {
        return (int) app(DatabaseFallbackCache::class)->remember('articles_count_country_1', 1440, function () {
            return self::where('country_id', 1)->count();
        });
    }

    public static function getCachedLatest()
    {
        return app(DatabaseFallbackCache::class)->remember('latest_articles_50', 1440, function () {
            return self::where('country_id', 1)
                ->orderByRaw('id DESC')
                ->limit(50)
                ->get();
        });
    }

    /**
     * Um artigo de Angola (country_id=1) por id ou slug — o MySQL continua
     * a ser a fonte principal. So quando o MySQL falhar e que se consulta
     * o snapshot de emergencia; se nem o snapshot o tiver, atira-se a
     * excepcao em vez de inventar um 404 que pode nao ser verdade.
     *
     * @return self|null  null = confirmadamente nao existe (404 real).
     *
     * @throws \App\Exceptions\DatabaseUnavailableException
     */
    public static function findPublicById($id): ?self
    {
        return self::resolvePublic($id, fn () => self::where('country_id', 1)->where('id', $id)->first());
    }

    public static function findPublicBySlug($slug): ?self
    {
        return self::resolvePublic($slug, fn () => self::where('country_id', 1)->where('slug', $slug)->first());
    }

    protected static function resolvePublic($slugOrId, \Closure $liveQuery): ?self
    {
        try {
            $resultado = $liveQuery();
            app(DatabaseCircuitBreaker::class)->recordSuccess();

            return $resultado;
        } catch (Throwable $e) {
            if (! DatabaseAvailability::isUnavailable($e)) {
                throw $e;
            }

            // A mesma contabilidade de falhas que o DatabaseFallbackCache
            // faz: sem isto, visitar paginas individuais de vagas/artigos
            // durante uma falha nunca abriria o disjuntor por si so (cada
            // pedido tentava sempre a sua propria ligacao).
            app(DatabaseCircuitBreaker::class)->recordFailure();

            $data = app(EmergencySnapshotService::class)->findArticle($slugOrId);

            if ($data !== null && ((int) ($data['country_id'] ?? 0)) === 1) {
                // forceFill(), nao make() — ver o comentario equivalente em
                // Job::resolvePublic().
                return (new self)->forceFill($data);
            }

            throw DatabaseUnavailableException::from($e, (int) config('resilience.retry_after', 120));
        }
    }
}
