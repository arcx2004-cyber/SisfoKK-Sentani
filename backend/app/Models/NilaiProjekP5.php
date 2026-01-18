<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NilaiProjekP5 extends Model
{
    protected $fillable = [
        'siswa_id', 'projek_p5_id', 'profil_lulusan_id',
        'nilai', 'catatan'
    ];

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    public function projekP5(): BelongsTo
    {
        return $this->belongsTo(ProjekP5::class);
    }

    public function profilLulusan(): BelongsTo
    {
        return $this->belongsTo(ProfilLulusan::class);
    }
}
