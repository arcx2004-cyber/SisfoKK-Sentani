<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;

class Pendaftaran extends Model
{
    protected $fillable = [
        'ppdb_setting_id', 'nomor_pendaftaran', 'no_pendaftaran', 'nama_lengkap', 'jenis_kelamin',
        'tempat_lahir', 'tanggal_lahir', 'agama', 'alamat', 'asal_sekolah',
        'email', 'no_wa', 'nama_ayah', 'pekerjaan_ayah', 'nama_ibu',
        'pekerjaan_ibu', 'no_telepon_ortu', 'status', 'catatan_admin',
        'jenis_pendaftaran', 'tingkat', 'pas_foto'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->nomor_pendaftaran)) {
                $num = static::generateNomorPendaftaran();
                $model->nomor_pendaftaran = $num;
                
                // Also fill no_pendaftaran for backward compatibility if column exists
                try {
                    if (Schema::hasColumn('pendaftarans', 'no_pendaftaran')) {
                        $model->no_pendaftaran = $num;
                    }
                } catch (\Exception $e) {
                    // Ignore schema errors during boot if any
                }
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
