<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Jabatan;
use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\RuangKelas;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\Unit;
use App\Models\User;
use App\Models\WaliKelas;
use App\Models\AnggotaRombel;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedGuru();
        $this->seedMataPelajaran();
        $this->seedRuangKelasAndRombel();
        $this->seedSiswa();
        
        $this->command->info('Dummy data created successfully!');
        $this->command->info('- 10 Guru');
        $this->command->info('- 50 Siswa');
        $this->command->info('- 6 Rombel');
        $this->command->info('- Mata Pelajaran untuk setiap unit');
    }

    private function seedGuru(): void
    {
        $units = Unit::all();
        $jabatanGuru = Jabatan::firstOrCreate(
            ['nama' => 'Guru'],
            ['kode' => 'GRU', 'is_teaching' => true, 'deskripsi' => 'Tenaga Pendidik']
        );

        $guruData = [
            ['nama' => 'Budi Santoso', 'nip' => '198501012010011001', 'jk' => 'L', 'pendidikan' => 'S1 Pendidikan'],
            ['nama' => 'Siti Rahayu', 'nip' => '198602022010012002', 'jk' => 'P', 'pendidikan' => 'S1 PGSD'],
            ['nama' => 'Ahmad Wijaya', 'nip' => '198703032011011003', 'jk' => 'L', 'pendidikan' => 'S1 Matematika'],
            ['nama' => 'Dewi Lestari', 'nip' => '198804042011012004', 'jk' => 'P', 'pendidikan' => 'S1 Bahasa Indonesia'],
            ['nama' => 'Eko Prasetyo', 'nip' => '198905052012011005', 'jk' => 'L', 'pendidikan' => 'S1 IPA'],
            ['nama' => 'Fitri Handayani', 'nip' => '199006062012012006', 'jk' => 'P', 'pendidikan' => 'S1 Bahasa Inggris'],
            ['nama' => 'Gunawan Setiawan', 'nip' => '199107072013011007', 'jk' => 'L', 'pendidikan' => 'S1 IPS'],
            ['nama' => 'Hesti Wulandari', 'nip' => '199208082013012008', 'jk' => 'P', 'pendidikan' => 'S1 PAUD'],
            ['nama' => 'Irfan Hakim', 'nip' => '199309092014011009', 'jk' => 'L', 'pendidikan' => 'S1 Olahraga'],
            ['nama' => 'Julia Permata', 'nip' => '199410102014012010', 'jk' => 'P', 'pendidikan' => 'S1 Seni Budaya'],
        ];

        foreach ($guruData as $index => $data) {
            $unit = $units[$index % $units->count()];
            $email = strtolower(str_replace(' ', '.', $data['nama'])) . '@sisfokk.sch.id';
            
            // Check if guru already exists
            $existingGuru = Guru::where('nip', $data['nip'])->first();
            if ($existingGuru) {
                continue;
            }

            // Create or get user for guru
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $data['nama'],
                    'password' => Hash::make('guru123'),
                    'is_active' => true,
                ]
            );
            
            if (!$user->hasRole('ptk')) {
                $user->assignRole('ptk');
            }

            Guru::create([
                'user_id' => $user->id,
                'unit_id' => $unit->id,
                'jabatan_id' => $jabatanGuru->id,
                'nip' => $data['nip'],
                'nuptk' => '00' . rand(10000000000, 99999999999),
                'nama_lengkap' => $data['nama'],
                'jenis_kelamin' => $data['jk'],
                'tempat_lahir' => 'Jayapura',
                'tanggal_lahir' => fake()->dateTimeBetween('-50 years', '-25 years')->format('Y-m-d'),
                'alamat' => 'Jl. Sentani No. ' . ($index + 1),
                'no_telepon' => '08' . rand(1000000000, 9999999999),
                'pendidikan_terakhir' => $data['pendidikan'],
                'tanggal_bergabung' => '2020-07-15',
                'is_active' => true,
            ]);
        }
    }

    private function seedMataPelajaran(): void
    {
        $units = Unit::all();
        
        $mapelSD = [
            ['nama' => 'Pendidikan Agama Kristen', 'kode' => 'PAK', 'jenis' => 'wajib'],
            ['nama' => 'Pendidikan Pancasila', 'kode' => 'PPKN', 'jenis' => 'wajib'],
            ['nama' => 'Bahasa Indonesia', 'kode' => 'BIND', 'jenis' => 'wajib'],
            ['nama' => 'Matematika', 'kode' => 'MTK', 'jenis' => 'wajib'],
            ['nama' => 'Ilmu Pengetahuan Alam', 'kode' => 'IPA', 'jenis' => 'wajib'],
            ['nama' => 'Ilmu Pengetahuan Sosial', 'kode' => 'IPS', 'jenis' => 'wajib'],
            ['nama' => 'Seni Budaya', 'kode' => 'SBK', 'jenis' => 'wajib'],
            ['nama' => 'Pendidikan Jasmani', 'kode' => 'PJOK', 'jenis' => 'wajib'],
            ['nama' => 'Bahasa Inggris', 'kode' => 'BING', 'jenis' => 'wajib'],
            ['nama' => 'Bahasa Daerah', 'kode' => 'BADA', 'jenis' => 'muatan_lokal'],
        ];

        $mapelSMP = [
            ['nama' => 'Pendidikan Agama Kristen', 'kode' => 'PAK', 'jenis' => 'wajib'],
            ['nama' => 'Pendidikan Pancasila', 'kode' => 'PPKN', 'jenis' => 'wajib'],
            ['nama' => 'Bahasa Indonesia', 'kode' => 'BIND', 'jenis' => 'wajib'],
            ['nama' => 'Matematika', 'kode' => 'MTK', 'jenis' => 'wajib'],
            ['nama' => 'Ilmu Pengetahuan Alam', 'kode' => 'IPA', 'jenis' => 'wajib'],
            ['nama' => 'Ilmu Pengetahuan Sosial', 'kode' => 'IPS', 'jenis' => 'wajib'],
            ['nama' => 'Bahasa Inggris', 'kode' => 'BING', 'jenis' => 'wajib'],
            ['nama' => 'Seni Budaya', 'kode' => 'SBK', 'jenis' => 'wajib'],
            ['nama' => 'Pendidikan Jasmani', 'kode' => 'PJOK', 'jenis' => 'wajib'],
            ['nama' => 'Informatika', 'kode' => 'INF', 'jenis' => 'wajib'],
            ['nama' => 'Bahasa Daerah', 'kode' => 'BADA', 'jenis' => 'muatan_lokal'],
        ];

        $mapelTK = [
            ['nama' => 'Nilai Agama dan Budi Pekerti', 'kode' => 'NABP', 'jenis' => 'wajib'],
            ['nama' => 'Jati Diri', 'kode' => 'JD', 'jenis' => 'wajib'],
            ['nama' => 'Dasar Literasi dan STEAM', 'kode' => 'DLS', 'jenis' => 'wajib'],
        ];

        foreach ($units as $unit) {
            $mapels = match($unit->kode) {
                'TK' => $mapelTK,
                'SD' => $mapelSD,
                'SMP' => $mapelSMP,
                default => $mapelSD,
            };

            foreach ($mapels as $index => $mapel) {
                // Prefix kode with unit code for uniqueness
                $kode = $unit->kode . '-' . $mapel['kode'];
                
                MataPelajaran::firstOrCreate(
                    ['kode' => $kode],
                    [
                        'unit_id' => $unit->id,
                        'nama' => $mapel['nama'],
                        'jenis' => $mapel['jenis'],
                        'deskripsi' => 'Mata pelajaran ' . $mapel['nama'],
                        'urutan' => $index + 1,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    private function seedRuangKelasAndRombel(): void
    {
        $unitSD = Unit::where('kode', 'SD')->first();
        $unitSMP = Unit::where('kode', 'SMP')->first();
        // Use TahunAjaran instead of Semester for Rombel
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        $semester = Semester::where('is_active', true)->first(); // Still needed for WaliKelas if applicable, or remove? 
        // WaliKelas table check? Let's check WaliKelas schema too.
        
        $gurus = Guru::all();

        // Ruang Kelas dan Rombel untuk SD
        $kelasSD = ['4A', '4B', '5A', '5B'];
        foreach ($kelasSD as $index => $nama) {
            $tingkat = (int) substr($nama, 0, 1);
            
            $ruang = RuangKelas::firstOrCreate(
                ['unit_id' => $unitSD->id, 'nama' => 'Ruang ' . $nama],
                ['kode' => 'SD-' . $nama, 'kapasitas' => 30, 'is_active' => true]
            );

            $rombel = Rombel::firstOrCreate(
                ['unit_id' => $unitSD->id, 'nama' => 'Kelas ' . $nama, 'tahun_ajaran_id' => $tahunAjaran->id],
                [
                    'ruang_kelas_id' => $ruang->id,
                    'tingkat' => $tingkat,
                ]
            );

            // Assign Wali Kelas
            if (isset($gurus[$index])) {
                WaliKelas::firstOrCreate(
                    ['rombel_id' => $rombel->id, 'semester_id' => $semester->id],
                    ['guru_id' => $gurus[$index]->id]
                );
            }
        }

        // Ruang Kelas dan Rombel untuk SMP
        $kelasSMP = ['7A', '8A'];
        foreach ($kelasSMP as $index => $nama) {
            $tingkat = (int) substr($nama, 0, 1);
            
            $ruang = RuangKelas::firstOrCreate(
                ['unit_id' => $unitSMP->id, 'nama' => 'Ruang ' . $nama],
                ['kode' => 'SMP-' . $nama, 'kapasitas' => 32, 'is_active' => true]
            );

            $rombel = Rombel::firstOrCreate(
                ['unit_id' => $unitSMP->id, 'nama' => 'Kelas ' . $nama, 'tahun_ajaran_id' => $tahunAjaran->id],
                [
                    'ruang_kelas_id' => $ruang->id,
                    'tingkat' => $tingkat,
                ]
            );

            // Assign Wali Kelas
            $guruIndex = $index + count($kelasSD);
            if (isset($gurus[$guruIndex])) {
                WaliKelas::firstOrCreate(
                    ['rombel_id' => $rombel->id, 'semester_id' => $semester->id],
                    ['guru_id' => $gurus[$guruIndex]->id]
                );
            }
        }
    }

    private function seedSiswa(): void
    {
        $unitSD = Unit::where('kode', 'SD')->first();
        $unitSMP = Unit::where('kode', 'SMP')->first();
        $tahunAjaran = TahunAjaran::where('is_active', true)->first();
        
        $namaLaki = ['Andi', 'Bima', 'Candra', 'Dani', 'Eko', 'Fajar', 'Galih', 'Hadi', 'Irwan', 'Joko'];
        $namaPerempuan = ['Ani', 'Bunga', 'Citra', 'Dewi', 'Eka', 'Fitri', 'Gita', 'Hana', 'Indah', 'Julia'];
        $namaBelakang = ['Pratama', 'Wijaya', 'Kusuma', 'Saputra', 'Hidayat', 'Rahman', 'Setiawan', 'Permana', 'Nugroho', 'Wicaksono'];
        
        $rombelsSD = Rombel::where('unit_id', $unitSD->id)->where('tahun_ajaran_id', $tahunAjaran->id)->get();
        $rombelsSMP = Rombel::where('unit_id', $unitSMP->id)->where('tahun_ajaran_id', $tahunAjaran->id)->get();

        $siswaCount = 0;
        $nisStart = 2025001;

        // 40 Siswa SD (10 per rombel)
        foreach ($rombelsSD as $rombel) {
            for ($i = 0; $i < 10; $i++) {
                $isLaki = rand(0, 1);
                $namaDepan = $isLaki ? $namaLaki[array_rand($namaLaki)] : $namaPerempuan[array_rand($namaPerempuan)];
                $namaBelakangPilih = $namaBelakang[array_rand($namaBelakang)];
                $namaLengkap = $namaDepan . ' ' . $namaBelakangPilih;

                $siswa = Siswa::create([
                    'unit_id' => $unitSD->id,
                    'nis' => (string) ($nisStart + $siswaCount),
                    'nisn' => '00' . rand(10000000, 99999999),
                    'nik' => '91' . rand(1000000000000, 9999999999999),
                    'nama_lengkap' => $namaLengkap,
                    'jenis_kelamin' => $isLaki ? 'L' : 'P',
                    'tempat_lahir' => 'Jayapura',
                    'tanggal_lahir' => fake()->dateTimeBetween('-12 years', '-9 years')->format('Y-m-d'),
                    'agama' => 'Kristen Protestan',
                    'alamat' => 'Jl. Sentani No. ' . rand(1, 100),
                    'no_telepon' => '08' . rand(1000000000, 9999999999),
                    'nama_ayah' => 'Bapak ' . $namaBelakangPilih,
                    'pekerjaan_ayah' => ['PNS', 'Wiraswasta', 'Petani', 'Nelayan', 'Karyawan'][rand(0, 4)],
                    'nama_ibu' => 'Ibu ' . $namaBelakangPilih,
                    'pekerjaan_ibu' => ['Ibu Rumah Tangga', 'PNS', 'Wiraswasta', 'Guru'][rand(0, 3)],
                    'no_telepon_ortu' => '08' . rand(1000000000, 9999999999),
                    'tanggal_masuk' => '2025-07-15',
                    'status' => 'aktif',
                ]);

                // Add to rombel
                AnggotaRombel::create([
                    'rombel_id' => $rombel->id,
                    'siswa_id' => $siswa->id,
                ]);

                $siswaCount++;
            }
        }

        // 10 Siswa SMP (5 per rombel)
        foreach ($rombelsSMP as $rombel) {
            for ($i = 0; $i < 5; $i++) {
                $isLaki = rand(0, 1);
                $namaDepan = $isLaki ? $namaLaki[array_rand($namaLaki)] : $namaPerempuan[array_rand($namaPerempuan)];
                $namaBelakangPilih = $namaBelakang[array_rand($namaBelakang)];
                $namaLengkap = $namaDepan . ' ' . $namaBelakangPilih;

                $siswa = Siswa::create([
                    'unit_id' => $unitSMP->id,
                    'nis' => (string) ($nisStart + $siswaCount),
                    'nisn' => '00' . rand(10000000, 99999999),
                    'nik' => '91' . rand(1000000000000, 9999999999999),
                    'nama_lengkap' => $namaLengkap,
                    'jenis_kelamin' => $isLaki ? 'L' : 'P',
                    'tempat_lahir' => 'Jayapura',
                    'tanggal_lahir' => fake()->dateTimeBetween('-15 years', '-12 years')->format('Y-m-d'),
                    'agama' => 'Kristen Protestan',
                    'alamat' => 'Jl. Sentani No. ' . rand(1, 100),
                    'no_telepon' => '08' . rand(1000000000, 9999999999),
                    'nama_ayah' => 'Bapak ' . $namaBelakangPilih,
                    'pekerjaan_ayah' => ['PNS', 'Wiraswasta', 'Petani', 'Nelayan', 'Karyawan'][rand(0, 4)],
                    'nama_ibu' => 'Ibu ' . $namaBelakangPilih,
                    'pekerjaan_ibu' => ['Ibu Rumah Tangga', 'PNS', 'Wiraswasta', 'Guru'][rand(0, 3)],
                    'no_telepon_ortu' => '08' . rand(1000000000, 9999999999),
                    'tanggal_masuk' => '2025-07-15',
                    'status' => 'aktif',
                ]);

                // Add to rombel
                AnggotaRombel::create([
                    'rombel_id' => $rombel->id,
                    'siswa_id' => $siswa->id,
                ]);

                $siswaCount++;
            }
        }
    }
}
