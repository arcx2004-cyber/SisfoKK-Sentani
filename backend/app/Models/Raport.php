<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Raport extends Model
{
    protected $fillable = [
        'siswa_id', 'rombel_id', 'semester_id', 'status',
        'approved_by', 'approved_at', 'printed_at',
        'catatan_wali_kelas', 'catatan_kepala_sekolah'
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'printed_at' => 'datetime',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function rombel(): BelongsTo
    {
        return $this->belongsTo(Rombel::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved' || $this->status === 'printed';
    }

    public function canPrint(): bool
    {
        return $this->isApproved();
    }
}
