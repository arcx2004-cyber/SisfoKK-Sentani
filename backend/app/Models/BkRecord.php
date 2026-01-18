<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BkRecord extends Model
{
    protected $fillable = [
        'siswa_id', 'guru_id', 'semester_id', 'tahun_ajaran_id', 
        'tanggal', 'jenis', 'skor',
        'deskripsi', 'tindak_lanjut', 'is_confidential'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_confidential' => 'boolean',
        'skor' => 'integer',
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
