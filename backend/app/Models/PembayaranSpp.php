<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranSpp extends Model
{
    protected $fillable = [
        'siswa_id', 'tarif_spp_id', 'bulan', 'tahun', 'nominal', 'nominal_bayar',
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

    public function tarifSpp(): BelongsTo
    {
        return $this->belongsTo(TarifSpp::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getNamaBulanAttribute(): string
    {
        $bulanNames = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulanNames[$this->bulan] ?? '';
    }
}
