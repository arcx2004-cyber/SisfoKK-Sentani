<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jabatan extends Model
{
    protected $fillable = ['nama', 'kode', 'deskripsi', 'is_teaching'];

    protected $casts = [
        'is_teaching' => 'boolean',
    ];

    public function gurus(): HasMany
    {
        return $this->hasMany(Guru::class);
    }
}
