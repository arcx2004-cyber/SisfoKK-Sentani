<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanEkskul extends Model
{
    protected $fillable = ['ekstrakurikuler_id', 'nama_kegiatan', 'tanggal', 'deskripsi'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function ekstrakurikuler(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Ekstrakurikuler::class);
    }
}
