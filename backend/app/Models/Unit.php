<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    protected $fillable = [
        'nama', 'kode', 'deskripsi', 'sekilas', 'konten', 'foto_sekolah',
        'foto_kepala_sekolah', 'kepala_sekolah', 'guru_id', 'visi', 'misi', 'fasilitas',
        'jam_belajar', 'telepon', 'email', 'urutan', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function kepalaSekolahGuru(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function gurus(): HasMany
    {
        return $this->hasMany(Guru::class);
    }

    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }

    public function ruangKelas(): HasMany
    {
        return $this->hasMany(RuangKelas::class);
    }

    public function mataPelajarans(): HasMany
    {
        return $this->hasMany(MataPelajaran::class);
    }

    public function rombels(): HasMany
    {
        return $this->hasMany(Rombel::class);
    }

    public function ekstrakurikulers(): HasMany
    {
        return $this->hasMany(Ekstrakurikuler::class);
    }

    public function tarifSpps(): HasMany
    {
        return $this->hasMany(TarifSpp::class);
    }

    public function tarifKegiatans(): HasMany
    {
        return $this->hasMany(TarifKegiatan::class);
    }

    public function ppdbSettings(): HasMany
    {
        return $this->hasMany(PpdbSetting::class);
    }
}
