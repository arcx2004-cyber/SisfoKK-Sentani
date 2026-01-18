<?php

namespace App\Filament\Widgets;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Rombel;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $user = Auth::user();
        
        // Admin/Kepsek sees all stats
        if ($user->hasAnyRole(['admin', 'kepsek'])) {
            return $this->getAdminStats();
        }
        
        // PTK/Guru sees their teaching stats
        if ($user->hasRole('ptk') && $user->guru) {
            return $this->getGuruStats($user->guru);
        }
        
        // Wali Kelas sees their rombel stats
        if ($user->hasRole('wali_kelas') && $user->guru) {
            return $this->getWaliKelasStats($user->guru);
        }
        
        // Siswa sees their own stats
        if ($user->hasRole('siswa') && $user->siswa) {
            return $this->getSiswaStats($user->siswa);
        }
        
        return [];
    }

    private function getAdminStats(): array
    {
        $totalGuru = Guru::whereHas('jabatan', fn($q) => $q->where('is_teaching', true))->count();
        $totalTendik = Guru::whereHas('jabatan', fn($q) => $q->where('is_teaching', false))->count();
        $totalSiswa = Siswa::where('status', 'aktif')->count();
        $totalRombel = Rombel::count();

        return [
            Stat::make('Total Guru (PTK)', $totalGuru)
                ->description('Pendidik')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->extraAttributes(['style' => 'border-left: 5px solid #3b82f6']),
            Stat::make('Total Tendik', $totalTendik)
                ->description('Tenaga Kependidikan')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->extraAttributes(['style' => 'border-left: 5px solid #3b82f6']),
            Stat::make('Total Siswa Aktif', $totalSiswa)
                ->description('Semua Unit')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning')
                ->chart([15, 20, 18, 25, 22, 30, $totalSiswa])
                ->extraAttributes(['style' => 'border-left: 5px solid #3b82f6']),
            Stat::make('Total Rombel', $totalRombel)
                ->description('Rombongan Belajar')
                ->descriptionIcon('heroicon-m-rectangle-group')
                ->color('primary')
                ->extraAttributes(['style' => 'border-left: 5px solid #3b82f6']),
        ];
    }

    private function getGuruStats(Guru $guru): array
    {
        // Use jadwalGurus instead of guruMengajars because previous widget fix shows jadwal_gurus is the active table
        $kelasYangDiajar = $guru->jadwalGurus()->distinct('rombel_id')->count();
        
        // Count students in rombels where the teacher has a schedule
        $totalSiswaYangDiajar = Siswa::whereHas('anggotaRombels', function($q) use ($guru) {
            $q->whereHas('rombel', function($q2) use ($guru) {
                $q2->whereHas('jadwalGurus', fn($q3) => $q3->where('guru_id', $guru->id));
            });
        })->distinct('id')->count();

        $stats = [
            Stat::make('Kelas yang Diajar', $kelasYangDiajar)
                ->description('Semester ini')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary')
                ->extraAttributes(['style' => 'border-left: 5px solid #3b82f6']),
            Stat::make('Total Siswa', $totalSiswaYangDiajar)
                ->description('Yang Anda ajar')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->extraAttributes(['style' => 'border-left: 5px solid #3b82f6']),
        ];
        
        // Add Upcoming Event Stat
        $nextEvent = \App\Models\Kegiatan::published()
            ->upcoming()
            ->orderBy('tanggal_mulai')
            ->first();
            
        if ($nextEvent) {
            $stats[] = Stat::make('Agenda Berikutnya', $nextEvent->judul)
                ->description($nextEvent->tanggal_mulai->format('d M') . ' - ' . \Illuminate\Support\Str::limit($nextEvent->lokasi, 15))
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info')
                ->extraAttributes(['style' => 'border-left: 5px solid #3b82f6']);
        } else {
            $stats[] = Stat::make('Agenda Sekolah', 'Belum ada')
                ->description('Tidak ada kegiatan terdekat')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('gray')
                ->extraAttributes(['style' => 'border-left: 5px solid #3b82f6']);
        }

        return $stats;
    }

    private function getWaliKelasStats(Guru $guru): array
    {
        $activeSemester = \App\Models\Semester::where('is_active', true)->first();
        $waliKelas = $guru->waliKelas()->where('semester_id', $activeSemester?->id)->first();
        
        if (!$waliKelas) {
            return [
                Stat::make('Status', 'Belum ditugaskan')
                    ->description('Sebagai Wali Kelas')
                    ->color('warning'),
            ];
        }

        $rombel = $waliKelas->rombel;
        $totalSiswa = $rombel->anggotaRombels()->count();
        $mapelDiKelas = $rombel->jadwalGurus()->distinct('mata_pelajaran_id')->count();

        return [
            Stat::make('Kelas Perwalian', $rombel->nama)
                ->description('Rombel Anda')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary')
                ->extraAttributes(['style' => 'border-left: 5px solid #3b82f6']),
            Stat::make('Jumlah Siswa', $totalSiswa)
                ->description('Dalam rombel')
                ->descriptionIcon('heroicon-m-users')
                ->color('success')
                ->extraAttributes(['style' => 'border-left: 5px solid #3b82f6']),
            Stat::make('Mata Pelajaran', $mapelDiKelas)
                ->description('Di kelas ini')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('info')
                ->extraAttributes(['style' => 'border-left: 5px solid #3b82f6']),
        ];
    }

    private function getSiswaStats(Siswa $siswa): array
    {
        $currentRombel = $siswa->getCurrentRombel();
        $totalAbsen = $siswa->absensiSiswas()->count();
        $ekstrakurikuler = $siswa->rombelEkskuls()->count();

        return [
            Stat::make('Kelas', $currentRombel?->nama ?? 'Belum ada')
                ->description('Rombel saat ini')
                ->descriptionIcon('heroicon-m-home')
                ->color('primary')
                ->extraAttributes(['style' => 'border-left: 5px solid #3b82f6']),
            Stat::make('Kehadiran', $totalAbsen . ' hari')
                ->description('Tercatat')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success')
                ->extraAttributes(['style' => 'border-left: 5px solid #3b82f6']),
            Stat::make('Ekstrakurikuler', $ekstrakurikuler)
                ->description('Yang diikuti')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning')
                ->extraAttributes(['style' => 'border-left: 5px solid #3b82f6']),
        ];
    }
}
