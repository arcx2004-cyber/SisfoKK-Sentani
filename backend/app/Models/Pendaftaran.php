<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pendaftaran extends Model
{
    protected $fillable = [
        'ppdb_setting_id', 'nomor_pendaftaran', 'nama_lengkap', 'jenis_kelamin',
        'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat', 'asal_sekolah',
        'email', 'no_wa', 'nama_ayah', 'pekerjaan_ayah', 'nama_ibu',
        'pekerjaan_ibu', 'no_telepon_ortu', 'status', 'catatan_admin', 'no_pendaftaran'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->nomor_pendaftaran)) {
                $year = date('Y');
                $latest = static::whereYear('created_at', $year)->max('id') ?? 0;
                $sequence = str_pad($latest + 1, 4, '0', STR_PAD_LEFT);
                $model->nomor_pendaftaran = "REG-{$year}-{$sequence}";
            }
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->no_pendaftaran)) {
                $model->no_pendaftaran = static::generateNomorPendaftaran();
                // Ensure columns matching 'nomor_pendaftaran' in database vs 'no_pendaftaran' in migration are consistent.
                // Migration 1: nomor_pendaftaran. Migration 2: no_pendaftaran.
                // Assuming database has 'nomor_pendaftaran' from initial migration.
                // Let's check Pendaftaran model fillable in previous turn (Step 585): 'nomor_pendaftaran'.
                // So property should be 'nomor_pendaftaran'.
                $model->nomor_pendaftaran = $model->no_pendaftaran; 
            }
        });
    }

    public function ppdbSetting(): BelongsTo
    {
        return $this->belongsTo(PpdbSetting::class);
    }

    public function dokumenPendaftarans(): HasMany
    {
        return $this->hasMany(DokumenPendaftaran::class);
    }

    public static function generateNomorPendaftaran(): string
    {
        $tahun = date('Y');
        $count = static::whereYear('created_at', $tahun)->count() + 1;
        return sprintf('PPDB-%s-%04d', $tahun, $count);
    }
}
