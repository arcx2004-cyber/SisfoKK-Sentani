<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MataPelajaran extends Model
{
    protected $fillable = ['unit_id', 'nama', 'kode', 'deskripsi', 'jenis', 'urutan', 'is_active', 'kkm', 'model_penilaian'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function capaianPembelajarans(): HasMany
    {
        return $this->hasMany(CapaianPembelajaran::class);
    }

    public function guruMengajars(): HasMany
    {
        return $this->hasMany(GuruMengajar::class);
    }

    public function nilaiAkhirs(): HasMany
    {
        return $this->hasMany(NilaiAkhir::class);
    }

    public function jadwalGurus(): HasMany
    {
        return $this->hasMany(JadwalGuru::class);
    }
}
