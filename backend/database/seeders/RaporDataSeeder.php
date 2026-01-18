<?php

namespace Database\Seeders;

use App\Models\CatatanRapor;
use App\Models\MataPelajaran;
use App\Models\PenilaianSikap;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Rombel;
use Illuminate\Database\Seeder;

class RaporDataSeeder extends Seeder
{
    public function run(): void
    {
        $siswa = Siswa::first();
        if (!$siswa) {
            $this->command->warn('No siswa found.');
            return;
        }

        $rombel = $siswa->rombels()->first();
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        $semester = Semester::where('is_active', true)->first();

        if (!$rombel || !$tahunAjaran || !$semester) {
             $this->command->warn('Missing rombel/ta/semester data.');
             return;
        }

        // Seed Sikap
        PenilaianSikap::updateOrCreate(
            [
                'siswa_id' => $siswa->id,
                'rombel_id' => $rombel->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'semester_id' => $semester->id,
            ],
            [
                'kedisiplinan' => 'A',
                'kejujuran' => 'A',
                'kesopanan' => 'A',
                'kebersihan' => 'B',
                'kepedulian' => 'A',
                'tanggung_jawab' => 'A',
                'percaya_diri' => 'B',
            ]
        );

        // Seed Catatan
        CatatanRapor::updateOrCreate(
             [
                'siswa_id' => $siswa->id,
                'rombel_id' => $rombel->id,
                'tahun_ajaran_id' => $tahunAjaran->id,
                'semester_id' => $semester->id,
            ],
            [
                'catatan' => 'Siswa menunjukkan perkembangan yang sangat baik dalam akademik maupun karakter. Pertahankan prestasinya.',
            ]
        );
        
        $this->command->info('Rapor data seeded for Siswa: ' . $siswa->nama_lengkap);
    }
}
