<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CapaianNilaiResource\Pages;
use App\Models\NilaiAkhir;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CapaianNilaiResource extends Resource
{
    protected static ?string $model = NilaiAkhir::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Laporan Akademik';
    protected static ?string $slug = 'laporan-akademik';
    protected static ?int $navigationSort = 5;

    public static function canViewAny(): bool
    {
        return Auth::user() && Auth::user()->hasRole('siswa');
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('semester.tahunAjaran.tahun')
                    ->label('Tahun Ajaran')
                    ->sortable(),
                Tables\Columns\TextColumn::make('semester.ganjil_genap')
                    ->label('Semester')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        '1', 'ganjil' => 'Ganjil',
                        '2', 'genap' => 'Genap',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nilai_pengetahuan')
                    ->label('Pengetahuan')
                    ->color(fn ($state) => $state < 75 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('nilai_keterampilan')
                    ->label('Keterampilan')
                    ->color(fn ($state) => $state < 75 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('nilai_akhir')
                    ->label('Nilai Akhir')
                    ->weight('bold')
                    ->color(fn ($state) => $state < 75 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('predikat')
                    ->label('Predikat')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'A' => 'success',
                        'B' => 'info',
                        'C' => 'warning',
                        'D', 'E' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('siswa', fn ($q) => $q->where('user_id', Auth::id()));
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCapaianNilais::route('/'),
        ];
    }
}
