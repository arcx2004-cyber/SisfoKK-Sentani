<?php

namespace Database\Seeders;

use App\Models\ModelPenilaian;
use App\Models\ProfilLulusan;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedUnits();
        $this->seedTahunAjaran();
        $this->seedModelPenilaian();
        $this->seedProfilLulusan();
    }

    private function seedUnits(): void
    {
        $units = [
            ['nama' => 'Unit TK', 'kode' => 'TK', 'deskripsi' => 'Taman Kanak-Kanak', 'urutan' => 1],
            ['nama' => 'Unit SD', 'kode' => 'SD', 'deskripsi' => 'Sekolah Dasar', 'urutan' => 2],
            ['nama' => 'Unit SMP', 'kode' => 'SMP', 'deskripsi' => 'Sekolah Menengah Pertama', 'urutan' => 3],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['kode' => $unit['kode']], $unit);
        }
    }

    private function seedTahunAjaran(): void
    {
        $tahunAjarans = [
            [
                'nama' => '2025/2026',
                'tanggal_mulai' => '2025-07-14',
                'tanggal_selesai' => '2026-06-30',
                'is_active' => true,
            ],
            [
                'nama' => '2026/2027',
                'tanggal_mulai' => '2026-07-13',
                'tanggal_selesai' => '2027-06-30',
                'is_active' => false,
            ],
            [
                'nama' => '2027/2028',
                'tanggal_mulai' => '2027-07-12',
                'tanggal_selesai' => '2028-06-30',
                'is_active' => false,
            ],
        ];

        foreach ($tahunAjarans as $ta) {
            $tahunAjaran = TahunAjaran::firstOrCreate(['nama' => $ta['nama']], $ta);

            // Create Semesters for each Tahun Ajaran
            Semester::firstOrCreate(
                ['tahun_ajaran_id' => $tahunAjaran->id, 'tipe' => 'ganjil'],
                [
                    'tanggal_mulai' => $tahunAjaran->tanggal_mulai,
                    'tanggal_selesai' => date('Y-m-d', strtotime($tahunAjaran->tanggal_mulai . ' +5 months')),
                    'is_active' => $tahunAjaran->is_active,
                ]
            );

            Semester::firstOrCreate(
                ['tahun_ajaran_id' => $tahunAjaran->id, 'tipe' => 'genap'],
                [
                    'tanggal_mulai' => date('Y-m-d', strtotime($tahunAjaran->tanggal_mulai . ' +6 months')),
                    'tanggal_selesai' => $tahunAjaran->tanggal_selesai,
                    'is_active' => false,
                ]
            );
        }
    }

    private function seedModelPenilaian(): void
    {
        $models = [
            ['nama' => 'Sumatif Harian', 'kode' => 'SH', 'deskripsi' => 'Penilaian harian setelah materi selesai', 'urutan' => 1],
            ['nama' => 'Sumatif Tengah Semester', 'kode' => 'STS', 'deskripsi' => 'Penilaian tengah semester', 'urutan' => 2],
            ['nama' => 'Sumatif Akhir Semester', 'kode' => 'SAS', 'deskripsi' => 'Penilaian akhir semester', 'urutan' => 3],
            ['nama' => 'Kenaikan Kelas', 'kode' => 'KK', 'deskripsi' => 'Penilaian untuk kenaikan kelas', 'urutan' => 4],
        ];

        foreach ($models as $model) {
            ModelPenilaian::firstOrCreate(['kode' => $model['kode']], $model);
        }
    }

    private function seedProfilLulusan(): void
    {
        // 8 Dimensi Profil Lulusan Kurikulum 2025 / Profil Pelajar Pancasila
        $profiles = [
            [
                'nama' => 'Beriman, Bertakwa kepada Tuhan YME, dan Berakhlak Mulia',
                'kode' => 'PL-1',
                'deskripsi' => 'Pelajar Indonesia yang beriman dan bertakwa kepada Tuhan YME dan berakhlak mulia, baik terhadap diri sendiri, sesama manusia, alam, dan lingkungan.',
                'urutan' => 1,
            ],
            [
                'nama' => 'Berkebinekaan Global',
                'kode' => 'PL-2',
                'deskripsi' => 'Pelajar Indonesia yang mempertahankan budaya luhur, lokalitas dan identitasnya, dan tetap berpikiran terbuka dalam berinteraksi dengan budaya lain.',
                'urutan' => 2,
            ],
            [
                'nama' => 'Bergotong Royong',
                'kode' => 'PL-3',
                'deskripsi' => 'Pelajar Indonesia yang memiliki kemampuan bergotong royong, yaitu kemampuan untuk melakukan kegiatan secara bersama-sama dengan suka rela.',
                'urutan' => 3,
            ],
            [
                'nama' => 'Mandiri',
                'kode' => 'PL-4',
                'deskripsi' => 'Pelajar Indonesia yang memiliki kemampuan untuk bertanggung jawab atas proses dan hasil belajarnya.',
                'urutan' => 4,
            ],
            [
                'nama' => 'Bernalar Kritis',
                'kode' => 'PL-5',
                'deskripsi' => 'Pelajar Indonesia yang mampu secara objektif memproses informasi baik kualitatif maupun kuantitatif, membangun keterkaitan antara berbagai informasi.',
                'urutan' => 5,
            ],
            [
                'nama' => 'Kreatif',
                'kode' => 'PL-6',
                'deskripsi' => 'Pelajar Indonesia yang kreatif mampu memodifikasi dan menghasilkan sesuatu yang orisinal, bermakna, bermanfaat, dan berdampak.',
                'urutan' => 6,
            ],
            [
                'nama' => 'Berjiwa Kepemimpinan',
                'kode' => 'PL-7',
                'deskripsi' => 'Pelajar Indonesia yang memiliki kemampuan memimpin diri sendiri dan orang lain dengan integritas dan tanggung jawab.',
                'urutan' => 7,
            ],
            [
                'nama' => 'Peduli Lingkungan',
                'kode' => 'PL-8',
                'deskripsi' => 'Pelajar Indonesia yang peduli terhadap kelestarian lingkungan dan berkontribusi dalam menjaga keberlanjutan alam.',
                'urutan' => 8,
            ],
        ];

        foreach ($profiles as $profile) {
            ProfilLulusan::firstOrCreate(['kode' => $profile['kode']], $profile);
        }
    }
}
