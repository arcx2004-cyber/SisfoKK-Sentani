<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RekapAbsensi extends Model
{
    protected $fillable = ['siswa_id', 'semester_id', 'hadir', 'sakit', 'izin', 'alpa'];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function getTotalKehadiranAttribute(): int
    {
        return $this->hadir + $this->sakit + $this->izin + $this->alpa;
    }
}
