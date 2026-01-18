<?php

namespace App\Filament\Widgets;

use App\Models\Siswa;
use App\Models\WaliKelas;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class RombelStudentsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Siswa Dalam Rombel';

    public static function canView(): bool
    {
        $user = Auth::user();
        return $user->hasRole('wali_kelas') && $user->guru;
    }

    public function table(Table $table): Table
    {
        $guru = Auth::user()->guru;
        $activeSemester = \App\Models\Semester::where('is_active', true)->first();
        $waliKelas = WaliKelas::where('guru_id', $guru?->id)
            ->where('semester_id', $activeSemester?->id)
            ->first();

        $rombelId = $waliKelas?->rombel_id;

        return $table
            ->query(
                Siswa::query()
                    ->whereHas('anggotaRombels', fn($q) => $q->where('rombel_id', $rombelId))
                    ->orderBy('nama_lengkap')
            )
            ->columns([
                Tables\Columns\TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis_kelamin')
                    ->label('L/P')
                    ->badge()
                    ->color(fn($state) => $state === 'L' ? 'info' : 'pink'),
                Tables\Columns\TextColumn::make('no_telepon')
                    ->label('No. Telp'),
            ])
            ->emptyStateHeading('Belum ada siswa dalam rombel')
            ->emptyStateIcon('heroicon-o-users');
    }
}
