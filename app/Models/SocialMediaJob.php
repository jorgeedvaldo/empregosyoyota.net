<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMediaJob extends Model
{
    use HasFactory;

    // Nome da tabela
    protected $table = 'social_media_jobs';

    // Colunas permitidas para mass assignment
    protected $fillable = [
        'job_id',
        'post_status',
    ];
}

