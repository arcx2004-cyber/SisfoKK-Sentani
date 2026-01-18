<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AnggotaKokurikuler extends Model
{
    protected $table = 'anggota_kokurikulers';

    protected $fillable = [
        'kokurikuler_id',
        'siswa_id',
        'semester_id',
    ];

    public function kokurikuler(): BelongsTo
    {
        return $this->belongsTo(Kokurikuler::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function nilai(): HasOne
    {
        return $this->hasOne(NilaiKokurikuler::class);
    }
}
