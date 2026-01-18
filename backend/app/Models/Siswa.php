<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Siswa extends Model
{
    protected $fillable = [
        'user_id', 'unit_id', 'nis', 'nisn', 'nik', 'nama_lengkap', 'jenis_kelamin',
        'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat', 'no_telepon',
        'nama_ayah', 'pekerjaan_ayah', 'nama_ibu', 'pekerjaan_ibu',
        'no_telepon_ortu', 'email_ortu', 'foto', 'tanggal_masuk', 'status'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_masuk' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function anggotaRombels(): HasMany
    {
        return $this->hasMany(AnggotaRombel::class);
    }

    public function rombels(): BelongsToMany
    {
        return $this->belongsToMany(Rombel::class, 'anggota_rombels');
    }

    public function nilaiSiswas(): HasMany
    {
        return $this->hasMany(NilaiSiswa::class);
    }

    public function nilaiAkhirs(): HasMany
    {
        return $this->hasMany(NilaiAkhir::class);
    }

    public function nilaiSpirituals(): HasMany
    {
        return $this->hasMany(NilaiSpiritual::class);
    }

    public function nilaiSosials(): HasMany
    {
        return $this->hasMany(NilaiSosial::class);
    }

    public function dataTubuhs(): HasMany
    {
        return $this->hasMany(DataTubuh::class);
    }

    public function catatanAkhirs(): HasMany
    {
        return $this->hasMany(CatatanAkhir::class);
    }

    public function rombelEkskuls(): HasMany
    {
        return $this->hasMany(RombelEkskul::class);
    }

    public function absensiSiswas(): HasMany
    {
        return $this->hasMany(AbsensiSiswa::class);
    }

    public function rekapAbsensis(): HasMany
    {
        return $this->hasMany(RekapAbsensi::class);
    }

    public function pembayaranSpps(): HasMany
    {
        return $this->hasMany(PembayaranSpp::class);
    }

    public function pembayaranKegiatans(): HasMany
    {
        return $this->hasMany(PembayaranKegiatan::class);
    }

    public function raports(): HasMany
    {
        return $this->hasMany(Raport::class);
    }

    public function bkRecords(): HasMany
    {
        return $this->hasMany(BkRecord::class);
    }

    public function kesehatans(): HasMany
    {
        return $this->hasMany(Kesehatan::class);
    }

    public function prestasis(): HasMany
    {
        return $this->hasMany(Prestasi::class);
    }

    public function getCurrentRombel(): ?Rombel
    {
        $activeSemester = Semester::getActive();
        if (!$activeSemester) return null;

        return $this->rombels()
            ->where('tahun_ajaran_id', $activeSemester->tahun_ajaran_id)
            ->first();
    }
}
