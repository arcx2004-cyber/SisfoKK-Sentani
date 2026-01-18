<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranKegiatan extends Model
{
    protected $fillable = [
        'siswa_id', 'tarif_kegiatan_id', 'nominal', 'nominal_bayar',
        'tanggal_bayar', 'status', 'metode_pembayaran', 'bukti_pembayaran', 'keterangan', 'created_by'
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'nominal_bayar' => 'decimal:2',
        'tanggal_bayar' => 'date',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tarifKegiatan(): BelongsTo
    {
        return $this->belongsTo(TarifKegiatan::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
