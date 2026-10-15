<?php

namespace App\Models;

use App\Services\DatabaseFallbackCache;
use App\Services\PublicPageCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

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
            self::clearCache();
        });

        static::deleted(function ($category) {
            self::clearCache();
        });
    }

    private static function clearCache()
    {
        $cache = app(DatabaseFallbackCache::class);
        $cache->forget('categories_all');
        $cache->forget('categories_with_jobs');

        Cache::forever('sitemap_version', ((int) Cache::get('sitemap_version', 1)) + 1);

        app(PublicPageCache::class)->bumpVersion();
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
