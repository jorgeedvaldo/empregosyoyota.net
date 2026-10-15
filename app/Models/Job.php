<?php

namespace App\Models;

use App\Services\DatabaseFallbackCache;
use App\Services\PublicPageCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Laravel\Scout\Searchable;

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
            self::clearCache($job);
        });

        static::deleted(function ($job) {
            self::clearCache($job);
        });
    }

    private static function clearCache($job)
    {
        // Invalida a copia normal. A copia de emergencia mantem-se de
        // proposito: e ela que serve o site se o MySQL cair a seguir.
        $cache = app(DatabaseFallbackCache::class);
        $cache->forget('latest_jobs_50');
        $cache->forget('categories_with_jobs');
        $cache->forget('job_' . $job->slug);
        $cache->forget('job_id_' . $job->id);
        $cache->forget('rss_feed_items');
        $cache->forget('jobs_count_country_1');

        Cache::forever('sitemap_version', ((int) Cache::get('sitemap_version', 1)) + 1);

        // O HTML guardado das paginas publicas ficou desactualizado.
        app(PublicPageCache::class)->bumpVersion();
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
}
