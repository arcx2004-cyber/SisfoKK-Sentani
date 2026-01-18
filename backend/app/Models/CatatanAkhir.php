<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CatatanAkhir extends Model
{
    protected $fillable = ['siswa_id', 'semester_id', 'catatan', 'nilai_clc', 'sakit', 'izin', 'alpha', 'uge_report', 'kokurikuler_catatan'];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }
}
