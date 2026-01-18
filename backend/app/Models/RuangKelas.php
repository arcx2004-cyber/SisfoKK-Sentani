<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RuangKelas extends Model
{
    protected $table = 'ruang_kelas';

    protected $fillable = ['unit_id', 'nama', 'kode', 'kapasitas', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function rombels(): HasMany
    {
        return $this->hasMany(Rombel::class);
    }

    public function jadwalGurus(): HasMany
    {
        return $this->hasMany(JadwalGuru::class);
    }
}
