<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiEkskul extends Model
{
    protected $fillable = ['kegiatan_ekskul_id', 'siswa_id', 'status', 'keterangan'];

    public function kegiatanEkskuls(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(KegiatanEkskul::class, 'kegiatan_ekskul_id');
    }

    public function siswa(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
