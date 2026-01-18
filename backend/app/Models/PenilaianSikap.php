<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianSikap extends Model
{
    use HasFactory;

    protected $fillable = [
        'siswa_id',
        'rombel_id',
        'tahun_ajaran_id',
        'semester_id',
        'kedisiplinan',
        'kejujuran',
        'kesopanan',
        'kebersihan',
        'kepedulian',
        'tanggung_jawab',
        'percaya_diri',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }
}
