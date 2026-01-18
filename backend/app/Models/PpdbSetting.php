<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PpdbSetting extends Model
{
    protected $fillable = [
        'unit_id', 'tahun_ajaran_id', 'tanggal_buka', 'tanggal_tutup',
        'alur_pendaftaran', 'persyaratan', 'biaya_pendaftaran', 'is_active'
    ];

    protected $casts = [
        'tanggal_buka' => 'date',
        'tanggal_tutup' => 'date',
        'biaya_pendaftaran' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function pendaftarans(): HasMany
    {
        return $this->hasMany(Pendaftaran::class);
    }

    public function isOpen(): bool
    {
        $now = now();
        return $this->is_active && $now->between($this->tanggal_buka, $this->tanggal_tutup);
    }
}
