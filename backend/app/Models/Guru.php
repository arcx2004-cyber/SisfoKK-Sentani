<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guru extends Model
{
    protected $fillable = [
        'user_id', 'unit_id', 'jabatan_id', 'nip', 'nuptk', 'nama_lengkap',
        'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'alamat', 'no_telepon',
        'pendidikan_terakhir', 'foto', 'tanggal_bergabung', 'is_active'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_bergabung' => 'date',
        'is_active' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function jabatan(): BelongsTo
    {
        return $this->belongsTo(Jabatan::class);
    }

    public function waliKelas(): HasMany
    {
        return $this->hasMany(WaliKelas::class);
    }

    public function guruMengajars(): HasMany
    {
        return $this->hasMany(GuruMengajar::class);
    }

    public function jadwalGurus(): HasMany
    {
        return $this->hasMany(JadwalGuru::class);
    }

    public function absensiGurus(): HasMany
    {
        return $this->hasMany(AbsensiGuru::class);
    }

    public function pelatihEkskuls(): HasMany
    {
        return $this->hasMany(PelatihEkskul::class);
    }

    public function bkRecords(): HasMany
    {
        return $this->hasMany(BkRecord::class);
    }

    public function isTeaching(): bool
    {
        return $this->jabatan?->is_teaching ?? false;
    }
}
