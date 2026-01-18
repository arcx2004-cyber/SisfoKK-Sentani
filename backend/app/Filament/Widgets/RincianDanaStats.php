<?php

namespace App\Filament\Widgets;

use App\Models\PembayaranKegiatan;
use App\Models\PembayaranSpp;
use App\Models\Rapbs;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class RincianDanaStats extends BaseWidget
{
    // Ensure it doesn't show up on dashboard automatically unless intended
    // protected static ?int $sort = 1; 

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin', 'administrator', 'kepala_sekolah', 'kepsek', 'bendahara']);
    } 

    protected function getStats(): array
    {
        $user = auth()->user();
        $unitId = null;
        
        // Determine Unit ID for Kepsek
        if ($user->hasRole(['kepala_sekolah', 'kepsek']) && $user->guru) {
            $unitId = $user->guru->unit_id;
        }

        // 1. Dana BOS/BOSP (From RAPBS)
        $bosp = 0;
        $activeYear = \App\Models\TahunAjaran::where('is_active', true)->first();
        
        if ($activeYear) {
             $query = Rapbs::where('tahun_ajaran_id', $activeYear->id);
             
             if ($unitId) {
                 $query->where('unit_id', $unitId);
             }
             
             $bosp1 = $query->sum('bosp_tahap_1');
             $bosp2 = $query->sum('bosp_tahap_2');
             $bosp = $bosp1 + $bosp2;
        }

        // 2. Uang SPP Masuk
        $sppQuery = PembayaranSpp::where('status', 'lunas');
        if ($unitId) {
            $sppQuery->whereHas('siswa', function($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            });
        }
        $uangSpp = $sppQuery->sum('nominal_bayar');

        // 3. Uang Kegiatan Masuk
        $kegiatanQuery = PembayaranKegiatan::where('status', 'lunas');
        if ($unitId) {
            $kegiatanQuery->whereHas('siswa', function($q) use ($unitId) {
                $q->where('unit_id', $unitId);
            });
        }
        $uangKegiatan = $kegiatanQuery->sum('nominal_bayar');

        return [
            Stat::make('BOSP Tahap 1', 'Rp ' . number_format($bosp1 ?? 0, 0, ',', '.'))
                ->description('Penerimaan Semester 1')
                ->color('info')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('BOSP Tahap 2', 'Rp ' . number_format($bosp2 ?? 0, 0, ',', '.'))
                ->description('Penerimaan Semester 2')
                ->color('info')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Total Anggaran (RAPBS)', 'Rp ' . number_format($bosp, 0, ',', '.'))
                ->description('Total dari Tahap 1 & 2')
                ->color('success')
                ->icon('heroicon-o-document-currency-dollar'),
            
            Stat::make('Pemasukan SPP', 'Rp ' . number_format($uangSpp, 0, ',', '.'))
                ->description('Total uang SPP diterima')
                ->color('primary')
                ->icon('heroicon-o-currency-dollar'),

            Stat::make('Pemasukan Kegiatan', 'Rp ' . number_format($uangKegiatan, 0, ',', '.'))
                ->description('Total uang kegiatan diterima')
                ->color('warning')
                ->icon('heroicon-o-ticket'),
        ];
    }
}
