<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rapbs extends Model
{
    protected $table = 'rapbs';

    protected $fillable = [
        'unit_id', 'tahun_ajaran_id', 'total_pendapatan', 'total_pengeluaran', 'alokasi_dana_kegiatan',
        'bosp_tahap_1', 'bosp_tahap_2',
        'status', 'catatan_direktur', 'created_by', 'approved_by'
    ];

    protected $casts = [
        'alokasi_dana_kegiatan' => 'decimal:2',
        'bosp_tahap_1' => 'decimal:2',
        'bosp_tahap_2' => 'decimal:2',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(RapbsDetail::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function calculateTotals()
    {
        $this->total_pendapatan = $this->details()->where('jenis', 'pendapatan')->sum('nominal');
        $this->total_pengeluaran = $this->details()->where('jenis', 'pengeluaran')->sum('nominal');
        $this->save();
    }
}
