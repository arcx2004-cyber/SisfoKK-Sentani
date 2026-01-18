<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class StudentGradesWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Nilai Mata Pelajaran';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user->hasRole('siswa') && $user->siswa;
    }

    public function table(Table $table): Table
    {
        $siswa = Auth::user()->siswa;
        $activeSemester = \App\Models\Semester::where('is_active', true)->first();

        return $table
            ->query(
                \App\Models\NilaiAkhir::query()
                    ->where('siswa_id', $siswa?->id)
                    ->where('semester_id', $activeSemester?->id)
                    ->with(['mataPelajaran'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nilai')
                    ->label('Nilai')
                    ->badge()
                    ->color(fn($state) => match(true) {
                        $state >= 90 => 'success',
                        $state >= 75 => 'info',
                        $state >= 60 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('deskripsi_capaian')
                    ->label('Deskripsi')
                    ->limit(50),
            ])
            ->emptyStateHeading('Belum ada nilai')
            ->emptyStateDescription('Nilai akan muncul setelah penilaian dilakukan')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }
}
