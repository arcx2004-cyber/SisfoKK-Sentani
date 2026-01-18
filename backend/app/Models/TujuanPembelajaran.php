<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TujuanPembelajaran extends Model
{
    protected $fillable = ['capaian_pembelajaran_id', 'kode', 'deskripsi', 'urutan'];

    public function capaianPembelajaran(): BelongsTo
    {
        return $this->belongsTo(CapaianPembelajaran::class);
    }
}
