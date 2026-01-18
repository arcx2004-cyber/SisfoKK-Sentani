<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class IncomeChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Pemasukan SPP Bulanan';

    public static function canView(): bool
    {
        return auth()->user()->hasRole(['tendik', 'super_admin']);
    }

    protected function getData(): array
    {
        $data = \App\Models\PembayaranSpp::selectRaw('MONTH(tanggal_bayar) as month, SUM(nominal_bayar) as total')
            ->whereYear('tanggal_bayar', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();
            
        return [
            'datasets' => [
                [
                    'label' => 'Income SPP',
                    'data' => array_values($data),
                ],
            ],
            'labels' => array_map(fn($m) => date('F', mktime(0, 0, 0, $m, 1)), array_keys($data)),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
