<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RapbsDetail extends Model
{
    protected $fillable = ['rapbs_id', 'jenis', 'sumber_dana', 'uraian', 'nominal', 'keterangan'];

    protected $casts = [
        'nominal' => 'decimal:2',
    ];

    public function rapbs(): BelongsTo
    {
        return $this->belongsTo(Rapbs::class);
    }
}
