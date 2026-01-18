<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiTujuanPembelajaran extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = ['siswa_id', 'tujuan_pembelajaran_id', 'nilai', 'deskripsi'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function tujuanPembelajaran()
    {
        return $this->belongsTo(TujuanPembelajaran::class);
    }
}
