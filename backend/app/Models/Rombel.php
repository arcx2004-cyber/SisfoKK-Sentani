<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Rombel extends Model
{
    protected $fillable = ['unit_id', 'ruang_kelas_id', 'tahun_ajaran_id', 'nama', 'tingkat'];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function ruangKelas(): BelongsTo
    {
        return $this->belongsTo(RuangKelas::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function waliKelas(): HasMany
    {
        return $this->hasMany(WaliKelas::class);
    }

    public function anggotaRombels(): HasMany
    {
        return $this->hasMany(AnggotaRombel::class);
    }

    public function siswas(): BelongsToMany
    {
        return $this->belongsToMany(Siswa::class, 'anggota_rombels');
    }

    public function guruMengajars(): HasMany
    {
        return $this->hasMany(GuruMengajar::class);
    }

    public function jadwalGurus(): HasMany
    {
        return $this->hasMany(JadwalGuru::class);
    }

    public function absensiSiswas(): HasMany
    {
        return $this->hasMany(AbsensiSiswa::class);
    }

    public function raports(): HasMany
    {
        return $this->hasMany(Raport::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->unit->kode . ' - Kelas ' . $this->nama;
    }
}
