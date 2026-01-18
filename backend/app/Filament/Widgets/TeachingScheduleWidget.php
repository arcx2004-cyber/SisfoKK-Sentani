<?php

namespace App\Filament\Widgets;

use App\Models\JadwalGuru;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TeachingScheduleWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Jadwal Mengajar Anda';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user->hasRole('ptk') && $user->guru;
    }

    public function table(Table $table): Table
    {
        $guru = Auth::user()->guru;
        $activeSemester = \App\Models\Semester::where('is_active', true)->first();

        return $table
            ->query(
                JadwalGuru::query()
                    ->where('guru_id', $guru?->id)
                    ->where('semester_id', $activeSemester?->id)
                    ->with(['mataPelajaran', 'rombel.ruangKelas'])
                    ->orderByRaw("FIELD(hari, 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu')")
                    ->orderBy('jam_mulai')
            )
            ->columns([
                Tables\Columns\TextColumn::make('hari')
                    ->label('Hari')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'senin' => 'success',
                        'selasa' => 'warning',
                        'rabu' => 'danger',
                        'kamis' => 'info',
                        'jumat' => 'primary',
                        'sabtu' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('jam_mulai')
                    ->label('Jam')
                    ->formatStateUsing(fn ($record) => $record->jam_mulai->format('H:i') . ' - ' . $record->jam_selesai->format('H:i')),
                Tables\Columns\TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rombel.nama')
                    ->label('Kelas'),
                Tables\Columns\TextColumn::make('rombel.ruangKelas.nama')
                    ->label('Ruang'),
            ])
            ->emptyStateHeading('Belum ada jadwal mengajar')
            ->emptyStateDescription('Hubungi admin jika jadwal belum muncul')
            ->emptyStateIcon('heroicon-o-calendar');
    }
}
