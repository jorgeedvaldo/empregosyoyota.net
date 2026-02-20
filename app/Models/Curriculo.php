<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'author', 'description', 'link', 'photo'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::created(function ($curriculo) {
            $curriculo->slug = $curriculo->generateSlug($curriculo->title, $curriculo->id);
            $curriculo->save();
        });
    }

    private function generateSlug($title, $id)
    {
        if (static::whereSlug($slug = Str::slug($title))->exists()) {
            $max = static::whereTitle($title)->latest('id');
            $slug = $slug . '-' . $id;
        }
        return $slug;
    }
}
