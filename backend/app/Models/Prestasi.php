<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestasi extends Model
{
    protected $fillable = ['siswa_id', 'semester_id', 'jenis', 'keterangan', 'judul', 'tanggal', 'foto', 'is_public'];

    protected $casts = [
        'tanggal' => 'date',
        'is_public' => 'boolean',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }
}
