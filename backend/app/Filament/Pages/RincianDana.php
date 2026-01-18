<?php

namespace App\Filament\Pages;

use App\Models\PembayaranKegiatan;
use App\Models\PembayaranSpp;
use App\Models\Rapbs;
use Filament\Pages\Page;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card; // Compatibility
use App\Filament\Widgets\RincianDanaStats;

class RincianDana extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Pembiayaan Sekolah';
    protected static ?string $title = 'Rincian Dana';
    protected static string $view = 'filament.pages.rincian-dana';

    // Only allow Kepsek (and Admins obviously)
    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole('super_admin')) {
             return false;
        }
        return auth()->user()->hasAnyRole(['kepala_sekolah', 'kepsek']);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RincianDanaStats::class,
        ];
    }
}
