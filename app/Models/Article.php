<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

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
            self::clearCache($article);
            $article->generateThumbnail();
        });

        static::deleted(function ($article) {
            self::clearCache($article);
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

    private static function clearCache($article)
    {
        Cache::forget('latest_articles_50');
        Cache::forget('article_' . $article->slug);
        Cache::forget('article_id_' . $article->id);
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
