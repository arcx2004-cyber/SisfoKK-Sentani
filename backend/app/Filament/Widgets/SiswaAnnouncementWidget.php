<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\News;
use Illuminate\Support\Facades\Auth;

class SiswaAnnouncementWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        return Auth::user() && Auth::user()->hasRole('siswa');
    }

    protected function getStats(): array
    {
        $count = News::where('status', 'published')->count();
        // Maybe count unread? For now just total available.
        
        return [
            Stat::make('Pemberitahuan Sekolah', $count)
                ->description('Informasi Terbaru')
                ->descriptionIcon('heroicon-m-bell')
                ->color('primary'),
        ];
    }
}
