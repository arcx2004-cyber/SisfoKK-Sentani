<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mading extends Model
{
    protected $fillable = ['title', 'content', 'image', 'author_name', 'status', 'published_at'];

    protected $casts = [
        'published_at' => 'datetime',
    ];
}
