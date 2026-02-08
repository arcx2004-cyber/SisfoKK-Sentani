<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sambutan extends Model
{
    use HasFactory;

    protected $fillable = [
        'jabatan',
        'nama',
        'foto',
        'konten',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}