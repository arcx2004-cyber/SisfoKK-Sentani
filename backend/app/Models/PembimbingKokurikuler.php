<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembimbingKokurikuler extends Model
{
    protected $table = 'pembimbing_kokurikulers';

    protected $fillable = [
        'kokurikuler_id',
        'guru_id',
        'nama_pembimbing',
        'no_telepon',
    ];

    public function kokurikuler(): BelongsTo
    {
        return $this->belongsTo(Kokurikuler::class);
    }

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class);
    }
}
