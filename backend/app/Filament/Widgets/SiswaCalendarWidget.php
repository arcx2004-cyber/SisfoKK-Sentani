<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Kegiatan;
use Illuminate\Support\Facades\Auth;

class SiswaCalendarWidget extends BaseWidget
{
    protected static ?string $heading = 'Kalender Akademik / Kegiatan Sekolah';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return Auth::user() && Auth::user()->hasRole('siswa');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Kegiatan::query()
                    ->where('is_published', true)
                    ->where('tanggal_mulai', '>=', now()->startOfYear()) // Show current year events? Or just upcoming?
                    ->orderBy('tanggal_mulai', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->description(fn (Kegiatan $record): string => $record->waktu_mulai ? $record->waktu_mulai->format('H:i') . ' WIB' : ''),
                Tables\Columns\TextColumn::make('judul')
                    ->label('Kegiatan')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('lokasi')
                    ->label('Lokasi')
                    ->icon('heroicon-o-map-pin'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'akan_datang' => 'info', // Assuming these status values, adjust as needed or use logic
                        'berlangsung' => 'warning',
                        'selesai' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(function ($record) {
                        if ($record->tanggal_selesai < now()) return 'Selesai';
                        if ($record->tanggal_mulai <= now() && $record->tanggal_selesai >= now()) return 'Berlangsung';
                        return 'Akan Datang';
                    }),
            ])
            ->paginated([5]);
    }
}
