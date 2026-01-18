<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TopikKokurikuler extends Model
{
    protected $fillable = ['kokurikuler_id', 'nama_topik', 'tanggal', 'deskripsi'];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function kokurikuler(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Kokurikuler::class);
    }
}
