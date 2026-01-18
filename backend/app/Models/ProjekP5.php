<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjekP5 extends Model
{
    protected $table = 'projek_p5s';

    protected $fillable = [
        'unit_id', 'tahun_ajaran_id', 'semester_id',
        'tema', 'judul', 'deskripsi', 'fase'
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tahunAjaran(): BelongsTo
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function dimensions(): BelongsToMany
    {
        return $this->belongsToMany(ProfilLulusan::class, 'projek_p5_dimensions');
    }

    public function nilaiProjekP5s(): HasMany
    {
        return $this->hasMany(NilaiProjekP5::class);
    }
}
