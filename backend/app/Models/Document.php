<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = ['title', 'file_path', 'type', 'description', 'is_public'];

    protected $casts = [
        'is_public' => 'boolean',
    ];
}
