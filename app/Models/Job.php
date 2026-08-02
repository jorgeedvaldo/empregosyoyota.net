<?php

namespace App\Models;

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
        Cache::forget('latest_jobs_50');
        Cache::forget('categories_with_jobs');
        Cache::forget('job_' . $job->slug);
        Cache::forget('job_id_' . $job->id);
        Cache::forget('rss_feed_items');
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

    public static function getCachedLatest()
    {
        // 1440 minutes = 24 hours
        return Cache::remember('latest_jobs_50', 1440, function () {
            return self::with('categories')
                ->where('country_id', 1)
                ->orderByRaw('id DESC')
                ->limit(50)
                ->get();
        });
    }
}
