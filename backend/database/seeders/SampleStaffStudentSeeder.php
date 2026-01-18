<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Unit;
use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleStaffStudentSeeder extends Seeder
{
    public function run(): void
    {
        $jabatanPtk = Jabatan::where('kode', 'PTK')->first();
        $jabatanTendik = Jabatan::where('kode', 'TENDIK')->first();
        $jabatanKepsek = Jabatan::where('kode', 'KEPSEK')->first();
        
        $units = Unit::all();

        // 1. Seed Kepala Sekolah (One for all/specific unit, here we assign to SD for example or create global)
        // Let's create a Kepsek for SD
        $kepsekUser = User::firstOrCreate(
            ['email' => 'kepsek@sisfokk.sch.id'],
            [
                'name' => 'Bapak Daniel Wospakrik, S.Pd, M.M',
                'password' => Hash::make('password123'),
                'is_active' => true,
            ]
        );
        $kepsekUser->assignRole('kepsek');
        // Note: Guru record for Kepsek is already created in the loop below if listed there, 
        // but let's ensure he has the correct Jabatan if he is in the list.
        
        // 2. Sample Guru for each Unit (PTK)
        $guruData = [
            'TK' => [
                ['nama_lengkap' => 'Ibu Maria Saragih, S.Pd', 'nip' => '1990001001', 'jenis_kelamin' => 'P'],
                ['nama_lengkap' => 'Ibu Elisabeth Wospakrik, S.Pd', 'nip' => '1990001002', 'jenis_kelamin' => 'P'],
                ['nama_lengkap' => 'Ibu Ruth Yoman, S.Pd', 'nip' => '1990001003', 'jenis_kelamin' => 'P'],
                ['nama_lengkap' => 'Ibu Debora Wambrauw, S.Pd', 'nip' => '1990001004', 'jenis_kelamin' => 'P'],
                ['nama_lengkap' => 'Ibu Sarah Kareth, S.Pd', 'nip' => '1990001005', 'jenis_kelamin' => 'P'],
            ],
            'SD' => [
                ['nama_lengkap' => 'Bapak Daniel Wospakrik, S.Pd, M.M', 'nip' => '1985002001', 'jenis_kelamin' => 'L', 'jabatan_kode' => 'KEPSEK'], // Overriding to KEPSEK
                ['nama_lengkap' => 'Ibu Martha Sroyer, S.Pd', 'nip' => '1988002002', 'jenis_kelamin' => 'P'],
                ['nama_lengkap' => 'Bapak Yohanis Waromi, S.Pd', 'nip' => '1987002003', 'jenis_kelamin' => 'L'],
                ['nama_lengkap' => 'Ibu Kristina Kareth, S.Pd', 'nip' => '1990002004', 'jenis_kelamin' => 'P'],
                ['nama_lengkap' => 'Bapak Paulus Yoman, S.Pd', 'nip' => '1986002005', 'jenis_kelamin' => 'L'],
                ['nama_lengkap' => 'Ibu Naomi Wambrauw, S.Pd', 'nip' => '1991002006', 'jenis_kelamin' => 'P'],
                ['nama_lengkap' => 'Bapak Simon Kareth, S.Pd', 'nip' => '1989002007', 'jenis_kelamin' => 'L'],
                ['nama_lengkap' => 'Ibu Esther Sroyer, S.Pd', 'nip' => '1992002008', 'jenis_kelamin' => 'P'],
            ],
            'SMP' => [
                ['nama_lengkap' => 'Bapak Samuel Yoman, S.Pd, M.Pd', 'nip' => '1983003001', 'jenis_kelamin' => 'L'],
                ['nama_lengkap' => 'Ibu Rebecca Waromi, S.Pd', 'nip' => '1987003002', 'jenis_kelamin' => 'P'],
                ['nama_lengkap' => 'Bapak Petrus Sroyer, S.Pd', 'nip' => '1985003003', 'jenis_kelamin' => 'L'],
                ['nama_lengkap' => 'Ibu Priskila Kareth, S.Pd', 'nip' => '1988003004', 'jenis_kelamin' => 'P'],
                ['nama_lengkap' => 'Bapak Andreas Yoman, S.Pd', 'nip' => '1984003005', 'jenis_kelamin' => 'L'],
                ['nama_lengkap' => 'Ibu Lidya Wambrauw, S.Pd', 'nip' => '1990003006', 'jenis_kelamin' => 'P'],
                ['nama_lengkap' => 'Bapak Yohanes Saragih, S.Pd', 'nip' => '1986003007', 'jenis_kelamin' => 'L'],
                ['nama_lengkap' => 'Ibu Yuliana Waromi, S.Pd', 'nip' => '1989003008', 'jenis_kelamin' => 'P'],
                ['nama_lengkap' => 'Bapak Markus Kareth, S.Pd', 'nip' => '1987003009', 'jenis_kelamin' => 'L'],
                ['nama_lengkap' => 'Ibu Feby Yoman, S.Pd', 'nip' => '1991003010', 'jenis_kelamin' => 'P'],
            ],
        ];

        // Add some Tendik (Success Administration)
        $tendikData = [
            ['nama_lengkap' => 'Ibu Admin Tata Usaha', 'nip' => '1995000001', 'jenis_kelamin' => 'P', 'unit_kode' => 'SD'],
            ['nama_lengkap' => 'Bapak Admin Keuangan', 'nip' => '1994000002', 'jenis_kelamin' => 'L', 'unit_kode' => 'SMP'],
        ];

        // Process Teachers/Kepsek
        foreach ($units as $unit) {
            if (isset($guruData[$unit->kode])) {
                foreach ($guruData[$unit->kode] as $guru) {
                    $isKepsek = isset($guru['jabatan_kode']) && $guru['jabatan_kode'] === 'KEPSEK';
                    
                    // Create User for Guru/Kepsek
                    $email = strtolower(str_replace([' ', ',', '.'], '', explode(' ', $guru['nama_lengkap'])[1] ?? 'guru')) . $guru['nip'] . '@sisfokk.sch.id';
                    
                    if ($isKepsek) {
                        $email = 'kepsek@sisfokk.sch.id'; // Override for known kepsek
                    }

                    $user = User::firstOrCreate(
                        ['email' => $email],
                        [
                            'name' => $guru['nama_lengkap'],
                            'password' => Hash::make('password123'),
                            'is_active' => true,
                        ]
                    );

                    // Assign Role
                    if ($isKepsek) {
                        $user->assignRole('kepsek');
                        $jabatanId = $jabatanKepsek?->id;
                    } else {
                        $user->assignRole('ptk');
                        $jabatanId = $jabatanPtk?->id;
                    }

                    Guru::updateOrCreate(
                        ['nip' => $guru['nip']],
                        [
                            'user_id' => $user->id,
                            'unit_id' => $unit->id,
                            'jabatan_id' => $jabatanId,
                            'nama_lengkap' => $guru['nama_lengkap'],
                            'jenis_kelamin' => $guru['jenis_kelamin'],
                            'tempat_lahir' => 'Jayapura',
                            'tanggal_lahir' => '1985-01-01',
                            'alamat' => 'Sentani, Papua',
                            'pendidikan_terakhir' => 'S1',
                            'tanggal_bergabung' => '2020-07-01',
                            'is_active' => true,
                        ]
                    );
                }
            }
        }

        // Process Tendik
        foreach ($tendikData as $tendik) {
            $unit = Unit::where('kode', $tendik['unit_kode'])->first();
            if (!$unit) continue;

            $email = 'admin.' . strtolower($tendik['unit_kode']) . '@sisfokk.sch.id';
             $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $tendik['nama_lengkap'],
                    'password' => Hash::make('password123'),
                    'is_active' => true,
                ]
            );
            $user->assignRole('tendik');

            Guru::updateOrCreate(
                ['nip' => $tendik['nip']],
                [
                    'user_id' => $user->id,
                    'unit_id' => $unit->id,
                    'jabatan_id' => $jabatanTendik?->id,
                    'nama_lengkap' => $tendik['nama_lengkap'],
                    'jenis_kelamin' => $tendik['jenis_kelamin'],
                    'tempat_lahir' => 'Jayapura',
                    'tanggal_lahir' => '1990-01-01',
                    'alamat' => 'Sentani, Papua',
                    'pendidikan_terakhir' => 'D3',
                    'tanggal_bergabung' => '2021-01-01',
                    'is_active' => true,
                ]
            );
        }

        // 3. Sample Siswa counts per unit
        $siswaCounts = [
            'TK' => 45,
            'SD' => 280,
            'SMP' => 185,
        ];

        $namaDepan = ['Adi', 'Budi', 'Citra', 'Dewa', 'Eka', 'Fajar', 'Gita', 'Hana', 'Indra', 'Joko', 
                      'Kevin', 'Lisa', 'Maya', 'Nadia', 'Oscar', 'Putri', 'Randi', 'Sari', 'Tami', 'Udin'];
        $namaBelakang = ['Wospakrik', 'Yoman', 'Kareth', 'Wambrauw', 'Sroyer', 'Waromi', 'Saragih'];

        foreach ($units as $unit) {
            if (isset($siswaCounts[$unit->kode])) {
                $count = $siswaCounts[$unit->kode];
                for ($i = 1; $i <= $count; $i++) {
                    $depan = $namaDepan[array_rand($namaDepan)];
                    $belakang = $namaBelakang[array_rand($namaBelakang)];
                    $nisn = sprintf('%s%04d', $unit->kode, $i);
                    $nis = 'NIS' . $nisn;
                    
                    // Create User for Siswa
                    // Username = NISN, Password = password123
                    // Email = nisn@siswa.sisfokk.sch.id (fake email for login uniqueness)
                    $user = User::firstOrCreate(
                        ['email' => $nisn . '@siswa.sisfokk.sch.id'],
                        [
                            'name' => $depan . ' ' . $belakang,
                            'password' => Hash::make('password123'),
                            'is_active' => true,
                        ]
                    );
                    $user->assignRole('siswa');

                    Siswa::updateOrCreate(
                        ['nisn' => $nisn],
                        [
                            'user_id' => $user->id,
                            'unit_id' => $unit->id,
                            'nis' => $nis,
                            'nama_lengkap' => $depan . ' ' . $belakang,
                            'jenis_kelamin' => rand(0, 1) ? 'L' : 'P',
                            'tempat_lahir' => 'Jayapura',
                            'tanggal_lahir' => date('Y-m-d', strtotime('-' . rand(6, 16) . ' years')),
                            'alamat' => 'Sentani, Papua',
                            'nama_ayah' => 'Bapak ' . $belakang,
                            'nama_ibu' => 'Ibu ' . $belakang,
                            'tanggal_masuk' => '2025-07-14',
                            'status' => 'aktif',
                        ]
                    );
                }
            }
        }

        $this->command->info('✅ RBAC Seeding Complete!');
        $this->command->info('   - Created and assigned roles to Gurus (PTK)');
        $this->command->info('   - Created and assigned roles to Tendik & Kepsek');
        $this->command->info('   - Created Users and assigned "siswa" role to all students');
    }
}
