<?php

namespace Database\Seeders;

use App\Models\Ekstrakurikuler;
use App\Models\Guru;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\RuangKelas;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\Unit;
use App\Models\WaliKelas;
use Illuminate\Database\Seeder;

class AcademicDataSeeder extends Seeder
{
    public function run(): void
    {
        $activeSemester = Semester::where('is_active', true)->first();
        $tahunAjaran = $activeSemester->tahun_ajaran_id;
        
        // Focus on SD unit for sample data to ensure we hit 10 items easily
        $unitSD = Unit::where('kode', 'SD')->first();
        if (!$unitSD) {
            $unitSD = Unit::first(); // Fallback
        }

        // 1. Mata Pelajaran (10 Data)
        $mapels = [
            ['nama' => 'Pendidikan Agama Kristen', 'kode' => 'PAK', 'deskripsi' => 'Pewartaan Injil dan nilai-nilai Kristiani'],
            ['nama' => 'Pendidikan Pancasila', 'kode' => 'PKN', 'deskripsi' => 'Pembentukan karakter kebangsaan'],
            ['nama' => 'Bahasa Indonesia', 'kode' => 'IND', 'deskripsi' => 'Kemampuan literasi dan komunikasi'],
            ['nama' => 'Matematika', 'kode' => 'MAT', 'deskripsi' => 'Logika dan perhitungan'],
            ['nama' => 'Ilmu Pengetahuan Alam', 'kode' => 'IPA', 'deskripsi' => 'Sains dan alam sekitar'],
            ['nama' => 'Ilmu Pengetahuan Sosial', 'kode' => 'IPS', 'deskripsi' => 'Masyarakat dan lingkungan'],
            ['nama' => 'Bahasa Inggris', 'kode' => 'ING', 'deskripsi' => 'Bahasa asing internasional'],
            ['nama' => 'Seni Budaya', 'kode' => 'SBK', 'deskripsi' => 'Kesenian dan keterampilan'],
            ['nama' => 'PJOK', 'kode' => 'PJK', 'deskripsi' => 'Pendidikan jasmani dan kesehatan'],
            ['nama' => 'Muatan Lokal', 'kode' => 'MULOK', 'deskripsi' => 'Budaya daerah Papua'],
        ];

        foreach ($mapels as $index => $mapel) {
            MataPelajaran::firstOrCreate(
                ['kode' => $mapel['kode'], 'unit_id' => $unitSD->id],
                [
                    'nama' => $mapel['nama'],
                    'deskripsi' => $mapel['deskripsi'],
                    'urutan' => $index + 1,
                    'is_active' => true,
                ]
            );
        }

        // 2. Ruang Kelas (10 Data)
        $ruangan = [
            ['nama' => 'Kelas 1A', 'kode' => 'R1A', 'kapasitas' => 28],
            ['nama' => 'Kelas 1B', 'kode' => 'R1B', 'kapasitas' => 28],
            ['nama' => 'Kelas 2A', 'kode' => 'R2A', 'kapasitas' => 28],
            ['nama' => 'Kelas 2B', 'kode' => 'R2B', 'kapasitas' => 28],
            ['nama' => 'Kelas 3A', 'kode' => 'R3A', 'kapasitas' => 30],
            ['nama' => 'Kelas 3B', 'kode' => 'R3B', 'kapasitas' => 30],
            ['nama' => 'Kelas 4A', 'kode' => 'R4A', 'kapasitas' => 30],
            ['nama' => 'Kelas 4B', 'kode' => 'R4B', 'kapasitas' => 30],
            ['nama' => 'Kelas 5A', 'kode' => 'R5A', 'kapasitas' => 32],
            ['nama' => 'Kelas 6A', 'kode' => 'R6A', 'kapasitas' => 32],
        ];

        foreach ($ruangan as $r) {
            RuangKelas::firstOrCreate(
                ['kode' => $r['kode'], 'unit_id' => $unitSD->id],
                [
                    'nama' => $r['nama'],
                    'kapasitas' => $r['kapasitas'],
                    'is_active' => true,
                ]
            );
        }

        // 3. Ekstrakurikuler (10 Data)
        $ekskuls = [
            ['nama' => 'Pramuka', 'deskripsi' => 'Wajib bagi semua siswa'],
            ['nama' => 'Futsal', 'deskripsi' => 'Pengembangan bakat sepak bola'],
            ['nama' => 'Basket', 'deskripsi' => 'Tim basket sekolah'],
            ['nama' => 'Paduan Suara', 'deskripsi' => 'Choir sekolah untuk pelayanan'],
            ['nama' => 'Seni Tari', 'deskripsi' => 'Tarian tradisional dan modern'],
            ['nama' => 'Robotik', 'deskripsi' => 'Coding dan merakit robot'],
            ['nama' => 'Drumband', 'deskripsi' => 'Marching band sekolah'],
            ['nama' => 'English Club', 'deskripsi' => 'Conversation and storytelling'],
            ['nama' => 'Sains Club', 'deskripsi' => 'Eksperimen IPA'],
            ['nama' => 'Dokter Kecil', 'deskripsi' => 'UKS dan kesehatan'],
        ];

        foreach ($ekskuls as $e) {
            Ekstrakurikuler::firstOrCreate(
                ['nama' => $e['nama'], 'unit_id' => $unitSD->id],
                [
                    'deskripsi' => $e['deskripsi'],
                    'is_active' => true,
                ]
            );
        }

        // 4. Rombel & Wali Kelas (10 Data)
        // Ensure we have enough Gurus in this unit
        $gurus = Guru::where('unit_id', $unitSD->id)->get();
        if ($gurus->count() < 10) {
            // Fetch gurus from other units if needed or create dummies?
            // Let's reuse gurus cyclically if not enough
            $gurus = Guru::all(); 
        }

        foreach ($ruangan as $index => $r) {
            // Find created ruang
            $ruang = RuangKelas::where('kode', $r['kode'])->first();
            
            // Create Rombel
            // Nama Rombel usually matches class name e.g. "1A"
            $namaRombel = str_replace('Kelas ', '', $r['nama']); // "1A"
            $tingkat = (int) substr($namaRombel, 0, 1); // "1"

            $rombel = Rombel::firstOrCreate(
                [
                    'nama' => $namaRombel,
                    'unit_id' => $unitSD->id,
                    'tahun_ajaran_id' => $tahunAjaran,
                ],
                [
                    'ruang_kelas_id' => $ruang->id,
                    'tingkat' => $tingkat,
                ]
            );

            // Assign Wali Kelas
            // Pick a guru cyclically
            $guru = $gurus[$index % $gurus->count()];
            
            WaliKelas::firstOrCreate(
                [
                    'rombel_id' => $rombel->id,
                    'semester_id' => $activeSemester->id,
                ],
                [
                    'guru_id' => $guru->id,
                ]
            );
        }

        $this->command->info('✅ Academic Data Seeded for SD: 10 Mapel, 10 Ruang, 10 Ekskul, 10 Rombel/Wali Kelas.');
    }
}
