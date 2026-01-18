<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TahunAjaran extends Model
{
    protected $fillable = ['nama', 'tanggal_mulai', 'tanggal_selesai', 'is_active'];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'is_active' => 'boolean',
    ];

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    public function tarifSpps(): HasMany
    {
        return $this->hasMany(TarifSpp::class);
    }

    public function tarifKegiatans(): HasMany
    {
        return $this->hasMany(TarifKegiatan::class);
    }

    public function rombels(): HasMany
    {
        return $this->hasMany(Rombel::class);
    }

    public function ppdbSettings(): HasMany
    {
        return $this->hasMany(PpdbSetting::class);
    }

    public static function getActive(): ?self
    {
        return static::where('is_active', true)->first();
    }
}
