<?php

namespace App\Models;

use App\Services\DatabaseFallbackCache;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /**
     * Le uma definicao (com cache).
     */
    public static function get(string $key, $default = null)
    {
        return app(DatabaseFallbackCache::class)->rememberForever('setting_' . $key, function () use ($key, $default) {
            $row = static::where('key', $key)->first();

            return $row ? $row->value : $default;
        });
    }

    /**
     * Grava/atualiza uma definicao e limpa a cache.
     */
    public static function set(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        app(DatabaseFallbackCache::class)->forget('setting_' . $key);
    }
}
