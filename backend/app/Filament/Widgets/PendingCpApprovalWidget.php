<?php

namespace App\Filament\Widgets;

use App\Models\CapaianPembelajaran;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class PendingCpApprovalWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'CP & TP Terbaru dari Guru';

    public static function canView(): bool
    {
        return Auth::user()->hasAnyRole(['admin', 'kepsek']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                CapaianPembelajaran::query()
                    ->with(['mataPelajaran', 'semester.tahunAjaran', 'unit'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->searchable(),
                Tables\Columns\TextColumn::make('unit.kode')
                    ->label('Unit')
                    ->badge(),
                Tables\Columns\TextColumn::make('fase')
                    ->label('Fase')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode CP'),
                Tables\Columns\TextColumn::make('tujuan_pembelajarans_count')
                    ->counts('tujuanPembelajarans')
                    ->label('TP'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->since(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Lihat')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => \App\Filament\Resources\CapaianPembelajaranResource::getUrl('edit', ['record' => $record])),
            ])
            ->emptyStateHeading('Belum ada CP & TP')
            ->emptyStateIcon('heroicon-o-book-open');
    }
}
