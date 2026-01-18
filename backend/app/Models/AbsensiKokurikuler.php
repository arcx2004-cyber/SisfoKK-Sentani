<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsensiKokurikuler extends Model
{
    protected $fillable = ['topik_kokurikuler_id', 'siswa_id', 'status', 'keterangan'];

    public function topikKokurikuler(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(TopikKokurikuler::class);
    }

    public function siswa(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }
}
