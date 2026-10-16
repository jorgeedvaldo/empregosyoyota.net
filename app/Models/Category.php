<?php

namespace App\Models;

use App\Services\DatabaseFallbackCache;
use App\Services\EmergencySnapshotService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function jobs()
    {
        return $this->belongsToMany('App\Models\Job', 'category_jobs');
    }

    protected static function boot()
    {
        parent::boot();

        static::saved(function ($category) {
            self::syncAfterCommit();
        });

        static::deleted(function ($category) {
            self::syncAfterCommit();
        });
    }

    /**
     * As categorias sao poucas e mudam raramente (so pelo painel), por
     * isso a sincronizacao volta a ler a lista completa do MySQL — ao
     * contrario de vagas/artigos, aqui nao vale a pena optimizar a
     * actualizar so uma entrada.
     */
    private static function syncAfterCommit(): void
    {
        DB::afterCommit(function () {
            self::clearCache();

            app(EmergencySnapshotService::class)->replaceCategories(
                self::orderBy('name')->get(['id', 'name'])->toArray()
            );
        });
    }

    private static function clearCache()
    {
        $cache = app(DatabaseFallbackCache::class);
        $cache->forget('categories_all');
        $cache->forget('categories_with_jobs');

        Cache::forever('sitemap_version', ((int) Cache::get('sitemap_version', 1)) + 1);
    }

    public static function getCachedAll()
    {
        return app(DatabaseFallbackCache::class)->remember('categories_all', 1440, function () {
            return self::orderBy('name')->get();
        });
    }

    public static function getCachedWithJobs()
    {
        return app(DatabaseFallbackCache::class)->remember('categories_with_jobs', 1440, function () {
            return self::withCount('jobs')->orderBy('name')->get();
        });
    }
}
