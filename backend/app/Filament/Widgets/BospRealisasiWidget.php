<?php

namespace App\Filament\Widgets;

use App\Models\Rapbs;
use App\Models\TahunAjaran;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BospRealisasiWidget extends BaseWidget
{
    // protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'kepala_sekolah', 'kepsek']);
    }

    protected function getStats(): array
    {
        $activeYear = TahunAjaran::where('is_active', true)->first();
        $user = auth()->user();
        
        if (!$activeYear) {
            return [
                Stat::make('Info', 'Tahun Ajaran Aktif tidak ditemukan')->color('gray'),
            ];
        }

        $query = Rapbs::where('tahun_ajaran_id', $activeYear->id);

        // Filter by Unit for Kepsek
        if ($user->hasAnyRole(['kepala_sekolah', 'kepsek']) && $user->guru) {
            $query->where('unit_id', $user->guru->unit_id);
        }

        // Calculations
        // Note: Check if we sum accross multiple units for Super Admin? 
        // If Super Admin, they might want to see Total for School or Per Unit.
        // Assuming Total for now if Admin, or user-specific if Kepsek.
        
        $bosp1 = $query->sum('bosp_tahap_1');
        $bosp2 = $query->sum('bosp_tahap_2');
        $total = $bosp1 + $bosp2;

        return [
            Stat::make('BOSP Tahap 1 (Jan-Jun)', 'Rp ' . number_format($bosp1, 0, ',', '.'))
                ->description('Penerimaan Semester 1')
                ->color('info')
                ->icon('heroicon-o-calendar'),
            
            Stat::make('BOSP Tahap 2 (Jul-Des)', 'Rp ' . number_format($bosp2, 0, ',', '.'))
                ->description('Penerimaan Semester 2')
                ->color('info')
                ->icon('heroicon-o-calendar'),

            Stat::make('Total BOSP Diterima', 'Rp ' . number_format($total, 0, ',', '.'))
                ->description('Total Tahun Ini')
                ->color('success')
                ->icon('heroicon-o-banknotes'),
        ];
    }
}
