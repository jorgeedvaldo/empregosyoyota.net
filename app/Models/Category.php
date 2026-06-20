<?php

namespace App\Models;

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
        Cache::forget('categories_all');
        Cache::forget('categories_with_jobs');
    }

    public static function getCachedAll()
    {
        return Cache::remember('categories_all', 1440, function () {
            return self::orderBy('name')->get();
        });
    }

    public static function getCachedWithJobs()
    {
        return Cache::remember('categories_with_jobs', 1440, function () {
            return self::withCount('jobs')->orderBy('name')->get();
        });
    }
}
