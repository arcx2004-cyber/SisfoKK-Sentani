<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kokurikuler extends Model
{
    protected $table = 'kokurikulers';

    protected $fillable = [
        'unit_id',
        'nama',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function pembimbings(): HasMany
    {
        return $this->hasMany(PembimbingKokurikuler::class);
    }

    public function anggotas(): HasMany
    {
        return $this->hasMany(AnggotaKokurikuler::class);
    }

    public function topikKokurikulers(): HasMany
    {
        return $this->hasMany(TopikKokurikuler::class);
    }
}
