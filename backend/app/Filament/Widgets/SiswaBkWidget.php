<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SiswaBkWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()->hasRole('siswa') && auth()->user()->siswa;
    }

    protected function getStats(): array
    {
        $siswa = auth()->user()->siswa;
        
        if (!$siswa) {
            return [];
        }

        $activeYear = \App\Models\TahunAjaran::where('is_active', true)->first();
        
        $poinPelanggaran = \App\Models\BkRecord::where('siswa_id', $siswa->id)
            ->where('tahun_ajaran_id', $activeYear?->id)
            ->where('jenis', 'pelanggaran')
            ->sum('skor');

        $statusSikap = match (true) {
            $poinPelanggaran == 0 => ['Sangat Baik', 'heroicon-m-face-smile', 'success'],
            $poinPelanggaran <= 10 => ['Baik', 'heroicon-m-face-smile', 'info'],
            $poinPelanggaran <= 25 => ['Cukup (Perlu Perhatian)', 'heroicon-m-face-frown', 'warning'],
            $poinPelanggaran <= 50 => ['Kurang (Peringatan 1)', 'heroicon-m-exclamation-triangle', 'danger'],
            default => ['Sangat Kurang (Panggilan Ortu)', 'heroicon-m-exclamation-circle', 'danger'],
        };

        $prestasi = \App\Models\BkRecord::where('siswa_id', $siswa->id)
             ->where('tahun_ajaran_id', $activeYear?->id)
             ->where('jenis', 'prestasi')
             ->latest()
             ->first();

        return [
            Stat::make('Status Sikap', $statusSikap[0])
                ->description('Semester Ini')
                ->descriptionIcon($statusSikap[1])
                ->color($statusSikap[2]),

            Stat::make('Poin Pelanggaran', $poinPelanggaran)
                ->description('Total Poin')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($poinPelanggaran > 0 ? 'warning' : 'success'),

            Stat::make('Prestasi Terbaru', $prestasi ? $prestasi->deskripsi : '-')
                ->description($prestasi ? 'Poin: +' . $prestasi->skor : 'Belum ada prestasi tercatat')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('primary'),
        ];
    }
}
