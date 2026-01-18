<?php

namespace Database\Seeders;

use App\Models\CapaianPembelajaran;
use App\Models\Guru;
use App\Models\Jabatan;
use App\Models\MataPelajaran;
use App\Models\NilaiSiswa;
use App\Models\NilaiTujuanPembelajaran;
use App\Models\Rombel;
use App\Models\RuangKelas;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\TujuanPembelajaran;
use App\Models\Unit;
use App\Models\User;
use App\Models\WaliKelas;
use App\Models\AnggotaRombel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VerificationDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Starting Verification Data Seeding...');

        $tahunAjaran = TahunAjaran::where('is_active', true)->firstOrFail();
        // Assuming we are grading for the active semester (Ganjil usually first)
        $semester = Semester::where('tahun_ajaran_id', $tahunAjaran->id)->where('is_active', true)->first();
        if (!$semester) {
            $semester = Semester::where('tahun_ajaran_id', $tahunAjaran->id)->firstOrFail();
        }

        $units = Unit::all();

        // 1. Ensure Teachers (Wali Kelas) exist for 18 Rombels
        $this->seedTeachers(20); // Prepare 20 extra teachers just in case

        // 2. Seed Rombels (6 per Unit)
        $this->seedRombels($units, $tahunAjaran, $semester);

        // 3. Seed CP & TP (Academic Data)
        $this->seedAcademicData($units, $semester);

        // 4. Seed Students & Grades
        $this->seedStudentsAndGrades($units, $tahunAjaran, $semester);
        
        $this->command->info('Verification Data Seeding Completed!');
    }

    private function seedTeachers(int $count): void
    {
        $jabatanGuru = Jabatan::firstOrCreate(
            ['kode' => 'GRU'],
            ['nama' => 'Guru', 'is_teaching' => true, 'deskripsi' => 'Tenaga Pendidik']
        );

        // Get unit SD as default for random assignment, we will adjust later if needed
        $unitSD = Unit::where('kode', 'SD')->first();

        for ($i = 1; $i <= $count; $i++) {
            $name = "Guru Dummy $i";
            $email = "guru.dummy.$i@sisfokk.sch.id";
            
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'is_active' => true,
                ]
            );
            $user->assignRole('ptk');

            Guru::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'unit_id' => $unitSD->id, // Default assign
                    'jabatan_id' => $jabatanGuru->id,
                    'nip' => 'DUMMY' . str_pad($i, 5, '0', STR_PAD_LEFT),
                    'nama_lengkap' => $name,
                    'jenis_kelamin' => $i % 2 == 0 ? 'P' : 'L',
                    'is_active' => true,
                ]
            );
        }
        $this->command->info("$count Dummy Teachers Checked/Created.");
    }

    private function seedRombels($units, $tahunAjaran, $semester): void
    {
        $gurus = Guru::where('is_active', true)->get();
        $guruIndex = 0;

        foreach ($units as $unit) {
            $rombelNames = [];
            $tingkats = [];

            if ($unit->kode === 'TK') {
                $rombelNames = ['TK A1', 'TK A2', 'TK B1', 'TK B2', 'TK B3', 'TK B4'];
                $tingkats = [0, 0, 0, 0, 0, 0]; // 0 for TK usually
            } elseif ($unit->kode === 'SD') {
                $rombelNames = ['1A', '2A', '3A', '4A', '5A', '6A'];
                $tingkats = [1, 2, 3, 4, 5, 6];
            } elseif ($unit->kode === 'SMP') {
                $rombelNames = ['7A', '7B', '8A', '8B', '9A', '9B'];
                $tingkats = [7, 7, 8, 8, 9, 9];
            }

            foreach ($rombelNames as $idx => $name) {
                // Adjust if array length mismatch (should match based on request: 6 per unit)
                $tingkat = $tingkats[$idx] ?? 1;

                $ruang = RuangKelas::firstOrCreate(
                    ['kode' => "{$unit->kode}-{$name}"],
                    [
                        'unit_id' => $unit->id,
                        'nama' => "Ruang $name",
                        'kapasitas' => 30,
                        'is_active' => true
                    ]
                );

                $rombel = Rombel::firstOrCreate(
                    ['unit_id' => $unit->id, 'nama' => "Kelas $name", 'tahun_ajaran_id' => $tahunAjaran->id],
                    [
                        'ruang_kelas_id' => $ruang->id,
                        'tingkat' => $tingkat,
                    ]
                );

                // Assign Wali Kelas
                if ($gurus->count() > 0) {
                    $guru = $gurus[$guruIndex % $gurus->count()];
                    
                    WaliKelas::updateOrCreate(
                        ['rombel_id' => $rombel->id, 'semester_id' => $semester->id],
                        ['guru_id' => $guru->id]
                    );

                    $guruIndex++;
                }
            }
        }
        $this->command->info("Rombels and Wali Kelas seeded.");
    }

    private function seedAcademicData($units, $semester): void
    {
        foreach ($units as $unit) {
            $mapels = MataPelajaran::where('unit_id', $unit->id)->get();
            
            foreach ($mapels as $mapel) {
                // Seed CP
                // Simple logic: 1 CP per Phase per Mapel
                // SD: Fase A (1-2), B (3-4), C (5-6). SMP: Fase D (7-9). TK: Fondasi.
                
                $fases = [];
                if ($unit->kode === 'TK') $fases = ['A']; // Workaround: Schema only supports A-D
                if ($unit->kode === 'SD') $fases = ['A', 'B', 'C'];
                if ($unit->kode === 'SMP') $fases = ['D'];

                foreach ($fases as $fase) {
                    $cp = CapaianPembelajaran::firstOrCreate(
                        [
                            'unit_id' => $unit->id,
                            'mata_pelajaran_id' => $mapel->id,
                            'fase' => $fase,
                        ],
                        [
                            'semester_id' => $semester->id, // Or null if generic? Assuming generic usually, but schema has it.
                            'kode' => "CP-{$mapel->kode}-{$fase}",
                            'deskripsi' => "Memahami konsep dasar {$mapel->nama} pada Fase {$fase}",
                        ]
                    );

                    // Seed TP (2-3 per CP)
                    for ($i = 1; $i <= 3; $i++) {
                        TujuanPembelajaran::firstOrCreate(
                            [
                                'capaian_pembelajaran_id' => $cp->id,
                                'kode' => "TP-{$cp->id}-{$i}",
                            ],
                            [
                                'deskripsi' => "Mampu menjelaskan dan menerapkan elemen ke-{$i} dari {$mapel->nama}",
                            ]
                        );
                    }
                }
            }
        }
        $this->command->info("CP and TP Data seeded.");
    }

    private function seedStudentsAndGrades($units, $tahunAjaran, $semester): void
    {
        $rombels = Rombel::where('tahun_ajaran_id', $tahunAjaran->id)->get();
        
        $nisStart = 3000;

        foreach ($rombels as $rombel) {
            $unit = $rombel->unit;
            // Determine Phase based on Rombel Tingkat for grading
            $fase = 'A'; // Default TK (Workaround)
            if ($unit->kode === 'SD') {
                if ($rombel->tingkat <= 2) $fase = 'A';
                elseif ($rombel->tingkat <= 4) $fase = 'B';
                else $fase = 'C';
            } elseif ($unit->kode === 'SMP') {
                $fase = 'D';
            }

            // Create 5 Students per Rombel
            for ($i = 1; $i <= 5; $i++) {
                $nis = $nisStart++;
                $nama = "Siswa {$rombel->nama} {$i}";
                
                $siswa = Siswa::firstOrCreate(
                    ['nis' => (string)$nis],
                    [
                        'unit_id' => $unit->id,
                        'nisn' => '999' . $nis,
                        'nama_lengkap' => $nama,
                        'jenis_kelamin' => rand(0, 1) ? 'L' : 'P',
                        'status' => 'aktif',
                        'tanggal_masuk' => now(),
                        'tempat_lahir' => 'Sentani',
                        'tanggal_lahir' => now()->subYears(7 + $rombel->tingkat),
                    ]
                );

                // Enroll
                AnggotaRombel::firstOrCreate([
                    'rombel_id' => $rombel->id,
                    'siswa_id' => $siswa->id,
                ]);

                // Create Grades (Nilai)
                // Get Mapels for this unit
                $mapels = MataPelajaran::where('unit_id', $unit->id)->get();
                
                foreach ($mapels as $mapel) {
                    // Find CP for this Mapel and Phase
                    $cp = CapaianPembelajaran::where('mata_pelajaran_id', $mapel->id)
                        ->where('fase', $fase)
                        ->first();

                    if (!$cp) continue; // Should not happen if seeded correctly

                    // 1. Nilai Akhir (Nilai Siswa table - often for SAS/Rapor)
                    // Random score 70-95
                    NilaiSiswa::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'capaian_pembelajaran_id' => $cp->id,
                            'semester_id' => $semester->id,
                        ],
                        [
                            'nilai' => rand(75, 95),
                            'deskripsi' => "Menunjukkan penguasaan yang baik dalam {$cp->deskripsi}",
                        ]
                    );

                    // 2. Nilai TP (Formative)
                    $tps = TujuanPembelajaran::where('capaian_pembelajaran_id', $cp->id)->get();
                    foreach ($tps as $tp) {
                        NilaiTujuanPembelajaran::updateOrCreate(
                            [
                                'siswa_id' => $siswa->id,
                                'tujuan_pembelajaran_id' => $tp->id,
                            ],
                            [
                                'nilai' => rand(70, 98),
                                'deskripsi' => 'Tercapai dengan optimal',
                            ]
                        );
                    }
                }

                // 3. Nilai Akhir (Final Subject Grade for SAS Report)
                foreach ($mapels as $mapel) {
                    \App\Models\NilaiAkhir::updateOrCreate(
                        [
                            'siswa_id' => $siswa->id,
                            'mata_pelajaran_id' => $mapel->id,
                            'semester_id' => $semester->id,
                        ],
                        [
                            'nilai' => rand(78, 92),
                            'deskripsi_capaian' => "Menunjukkan penguasaan yang sangat baik dalam memahami materi {$mapel->nama}. Perlu ditingkatkan dalam aspek praktik.",
                        ]
                    );
                }
            }
        }
        $this->command->info("Students and Grades seeded.");
    }
}
