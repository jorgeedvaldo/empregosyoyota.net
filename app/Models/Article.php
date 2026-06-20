<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class Article extends Model
{
    use HasFactory;

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
            self::clearCache($article);
        });

        static::deleted(function ($article) {
            self::clearCache($article);
        });
    }

    private static function clearCache($article)
    {
        Cache::forget('latest_articles_50');
        Cache::forget('article_' . $article->slug);
        Cache::forget('article_id_' . $article->id);
    }

    private function generateSlug($title, $id)
    {
        if (static::whereSlug($slug = Str::slug($title))->exists()) {
            $max = static::whereTitle($title)->latest('id');
            $slug = $slug . '-' . $id;
        }
        return $slug;
    }

    public static function getCachedLatest()
    {
        // 1440 minutes = 24 hours
        return Cache::remember('latest_articles_50', 1440, function () {
            return self::where('country_id', 1)
                ->orderByRaw('id DESC')
                ->limit(50)
                ->get();
        });
    }
}
