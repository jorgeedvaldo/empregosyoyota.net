<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Draft extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'slug', 'company', 'province', 'description', 'email_or_link', 'photo', 'country_id'
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
