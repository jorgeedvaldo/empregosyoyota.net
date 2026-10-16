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
use Laravel\Scout\Searchable;
use Throwable;

class Job extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'title', 'slug', 'company', 'province', 'description', 'email_or_link', 'photo'
    ];

	protected static function boot()
    {
        parent::boot();

        static::created(function ($job) {
            $job->slug = $job->generateSlug($job->title, $job->id);
            $job->save();

			SocialMediaJob::create([
                'job_id' => $job->id,
                'post_status' => 0,
            ]);
        });

        static::saved(function ($job) {
            self::syncAfterCommit($job, deleted: false);
        });

        static::deleted(function ($job) {
            self::syncAfterCommit($job, deleted: true);
        });
    }

    /**
     * Confirma primeiro a gravacao no MySQL (DB::afterCommit — executa
     * imediatamente se nao houver transaccao aberta, ou so depois do
     * commit se houver), so depois invalida a cache de leitura e pede a
     * sincronizacao do snapshot de emergencia com esta UNICA vaga, sem
     * reconstruir a lista inteira.
     */
    private static function syncAfterCommit(self $job, bool $deleted): void
    {
        DB::afterCommit(function () use ($job, $deleted) {
            self::clearCache($job);

            $snapshot = app(EmergencySnapshotService::class);

            $deleted
                ? $snapshot->removeJob($job->id)
                : $snapshot->upsertJob($job->toSnapshotArray());
        });
    }

    private static function clearCache($job)
    {
        // Invalida a copia normal. A copia de emergencia (DatabaseFallbackCache)
        // mantem-se de proposito: e ela que serve o site se o MySQL cair a seguir.
        $cache = app(DatabaseFallbackCache::class);
        $cache->forget('latest_jobs_50');
        $cache->forget('categories_with_jobs');
        $cache->forget('rss_feed_items');
        $cache->forget('jobs_count_country_1');

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

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category', 'category_jobs');
    }

    /**
     * Campos indexados pelo TNTSearch (via Laravel Scout) para a
     * pesquisa de vagas: titulo, empresa, localizacao e descricao.
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'company' => $this->company,
            'province' => $this->province,
            'description' => strip_tags((string) $this->description),
        ];
    }

	public function country()
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * O que o snapshot de emergencia guarda desta vaga — so os campos que
     * as vistas publicas (job.blade.php, jobs.blade.php, etc.) realmente
     * usam.
     */
    public function toSnapshotArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'company' => $this->company,
            'province' => $this->province,
            'description' => $this->description,
            'email_or_link' => $this->email_or_link,
            'photo' => $this->photo,
            'country_id' => $this->country_id,
            'created_at' => optional($this->created_at)->toDateTimeString(),
            'updated_at' => optional($this->updated_at)->toDateTimeString(),
        ];
    }

    /**
     * Total de vagas de Angola, para a paginacao.
     *
     * Corria em cada visita a primeira pagina de /empregos — uma contagem
     * da tabela inteira so para escrever o numero de paginas no fundo.
     */
    public static function getCachedCount(): int
    {
        return (int) app(DatabaseFallbackCache::class)->remember('jobs_count_country_1', 1440, function () {
            return self::where('country_id', 1)->count();
        });
    }

    public static function getCachedLatest()
    {
        return app(DatabaseFallbackCache::class)->remember('latest_jobs_50', 1440, function () {
            return self::with('categories')
                ->where('country_id', 1)
                ->orderByRaw('id DESC')
                ->limit(50)
                ->get();
        });
    }

    /**
     * Uma vaga de Angola (country_id=1) por id — o MySQL continua a ser a
     * fonte principal (consulta sempre que a base de dados responde, sem
     * cache nenhuma pela frente). So quando o MySQL falhar e que se
     * consulta o snapshot de emergencia; se nem o snapshot a tiver,
     * atira-se a excepcao em vez de inventar um 404 que pode nao ser
     * verdade.
     *
     * @return self|null  null = confirmadamente nao existe (404 real).
     *
     * @throws \App\Exceptions\DatabaseUnavailableException
     */
    public static function findPublicById($id): ?self
    {
        return self::resolvePublic(
            $id,
            fn () => self::with('categories')->where('country_id', 1)->where('id', $id)->first(),
            fn (array $data) => ((int) ($data['country_id'] ?? 0)) === 1 ? $data : null
        );
    }

    /**
     * Uma vaga publica por slug — sem filtro de pais, tal como o
     * comportamento original de /empregos/{slug} (os slugs sao unicos
     * globalmente, independentemente do pais).
     */
    public static function findPublicBySlug($slug): ?self
    {
        return self::resolvePublic(
            $slug,
            fn () => self::with('categories')->where('slug', $slug)->first(),
            fn (array $data) => $data
        );
    }

    /**
     * @param  \Closure $liveQuery        Consulta MySQL (sem falhas).
     * @param  \Closure $acceptSnapshot   Filtra/valida os dados do snapshot;
     *                                     devolve null para os rejeitar.
     */
    protected static function resolvePublic($slugOrId, \Closure $liveQuery, \Closure $acceptSnapshot): ?self
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

            $data = app(EmergencySnapshotService::class)->findJob($slugOrId);
            $data = $data !== null ? $acceptSnapshot($data) : null;

            if ($data !== null) {
                // forceFill(), nao make(): "created_at"/"updated_at" nao
                // estao em $fillable (make() ia-os deixar a null, e um
                // Carbon null faz o Blade confundir
                // @section('created_at', $job->created_at) com a forma em
                // bloco da directiva, abrindo um ob_start() que nunca mais
                // fecha). Os dados vem do nosso proprio snapshot, nao de
                // input de visitantes — nao ha problema de seguranca em
                // saltar a protecao de mass assignment aqui.
                //
                // O snapshot tambem nao guarda a relacao "categories" (as
                // vistas publicas nao a usam na pagina da vaga); define-a
                // vazia em vez de deixada por carregar, para um acesso
                // futuro a ->categories nao tentar uma nova consulta ao
                // MySQL precisamente no momento em que ele nao responde.
                return (new self)->forceFill($data)->setRelation('categories', collect());
            }

            throw DatabaseUnavailableException::from($e, (int) config('resilience.retry_after', 120));
        }
    }
}
