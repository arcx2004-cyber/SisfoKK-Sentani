<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class AttendanceChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Statistik Kehadiran Hari Ini';

    public static function canView(): bool
    {
        return !auth()->user()->hasRole('siswa');
    }

    protected function getData(): array
    {
        // Get today's attendance counts
        $counts = \App\Models\AbsensiSiswa::whereDate('created_at', today())
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // Ensure keys exist
        $statuses = ['hadir', 'sakit', 'izin', 'alpha'];
        $data = [];
        foreach ($statuses as $status) {
            $data[] = $counts[$status] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Siswa',
                    'data' => $data,
                    'backgroundColor' => ['#22c55e', '#eab308', '#3b82f6', '#ef4444'], 
                ],
            ],
            'labels' => ['Hadir', 'Sakit', 'Izin', 'Alpha'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
