<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ekstrakurikuler extends Model
{
    protected $fillable = ['unit_id', 'nama', 'deskripsi', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function pelatihEkskuls(): HasMany
    {
        return $this->hasMany(PelatihEkskul::class);
    }

    public function rombelEkskuls(): HasMany
    {
        return $this->hasMany(RombelEkskul::class);
    }

    public function kegiatanEkskuls(): HasMany
    {
        return $this->hasMany(KegiatanEkskul::class);
    }
}
