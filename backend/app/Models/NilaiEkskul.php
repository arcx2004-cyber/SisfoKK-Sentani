<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NilaiEkskul extends Model
{
    protected $fillable = ['rombel_ekskul_id', 'grade', 'deskripsi'];

    public function rombelEkskul(): BelongsTo
    {
        return $this->belongsTo(RombelEkskul::class);
    }
}
