<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Semester extends Model
{
    protected $fillable = ['tahun_ajaran_id', 'tipe', 'tanggal_mulai', 'tanggal_selesai', 'is_active'];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function waliKelas(): HasMany
    {
        return $this->hasMany(WaliKelas::class);
    }

    public function guruMengajars(): HasMany
    {
        return $this->hasMany(GuruMengajar::class);
    }

    public function capaianPembelajarans(): HasMany
    {
        return $this->hasMany(CapaianPembelajaran::class);
    }

    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }

    public function getFullNameAttribute(): string
    {
        return $this->tahunAjaran->nama . ' - ' . ucfirst($this->tipe);
    }
}
