<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModelPenilaian extends Model
{
    protected $table = 'model_penilaians';

    protected $fillable = [
        'nama',
        'kode',
        'deskripsi',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function nilaiSiswas(): HasMany
    {
        return $this->hasMany(NilaiSiswa::class);
    }
}
