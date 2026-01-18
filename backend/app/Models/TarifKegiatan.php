<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TarifKegiatan extends Model
{
    protected $fillable = ['unit_id', 'tahun_ajaran_id', 'nama_kegiatan', 'nominal', 'keterangan'];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function pembayaranKegiatans(): HasMany
    {
        return $this->hasMany(PembayaranKegiatan::class);
    }
}
