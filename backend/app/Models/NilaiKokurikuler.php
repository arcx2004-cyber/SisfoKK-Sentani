<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiKokurikuler extends Model
{
    protected $table = 'nilai_kokurikulers';

    protected $fillable = [
        'anggota_kokurikuler_id',
        'grade',
        'deskripsi',
    ];

    public function anggotaKokurikuler(): BelongsTo
    {
        return $this->belongsTo(AnggotaKokurikuler::class);
    }
}
