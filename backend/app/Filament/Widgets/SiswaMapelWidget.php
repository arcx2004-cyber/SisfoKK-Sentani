<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use App\Models\JadwalGuru;

class SiswaMapelWidget extends BaseWidget
{
    protected static ?string $heading = 'Rekapan Mata Pelajaran';
    protected static ?int $sort = 2; // Show after Calendar or Notification?

    public static function canView(): bool
    {
        return Auth::user() && Auth::user()->hasRole('siswa');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                JadwalGuru::query()
                    ->whereHas('rombel', function($q) {
                        $q->whereHas('anggotaRombels', function($sq) {
                            $student = Auth::user()->siswa;
                            if ($student) {
                                $sq->where('siswa_id', $student->id);
                            }
                        });
                    })
                    // Filter by Active Semester
                     ->whereHas('semester', function($q) {
                        $q->where('is_active', true);
                     })
                    ->with(['mataPelajaran', 'guru'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guru.nama_lengkap')
                    ->label('Guru Pengajar'),
                Tables\Columns\TextColumn::make('hari')
                    ->badge(),
                Tables\Columns\TextColumn::make('jam_mulai')
                    ->label('Jam')
                    ->formatStateUsing(function ($record) {
                        // Ensure it's formatted as H:i
                        $start = $record->jam_mulai instanceof \Carbon\Carbon ? $record->jam_mulai->format('H:i') : substr($record->jam_mulai, 0, 5);
                        $end = $record->jam_selesai instanceof \Carbon\Carbon ? $record->jam_selesai->format('H:i') : substr($record->jam_selesai, 0, 5);
                        return "{$start} - {$end}";
                    }),
            ])
            ->paginated([5]);
    }
}
