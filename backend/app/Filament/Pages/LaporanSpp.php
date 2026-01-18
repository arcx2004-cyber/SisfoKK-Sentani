<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use App\Models\PembayaranSpp;
use Illuminate\Database\Eloquent\Builder;

class LaporanSpp extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Laporan Pembayaran';
    protected static ?string $navigationLabel = 'Laporan SPP';
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.laporan-spp';

    public function table(Table $table): Table
    {
        return $table
            ->query(PembayaranSpp::query()->with(['siswa', 'siswa.unit', 'siswa.rombels']))
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas_siswa')
                    ->label('Kelas')
                    ->state(fn (PembayaranSpp $record) => $record->siswa?->getCurrentRombel()?->nama ?? '-'),
                Tables\Columns\TextColumn::make('bulan')
                    ->formatStateUsing(fn (string $state): string => match ((int)$state) {
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        default => $state,
                    })->sortable(),
                Tables\Columns\TextColumn::make('tahun'),
                Tables\Columns\TextColumn::make('nominal_bayar')
                    ->label('Bayar')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('tanggal_bayar')
                    ->date()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'lunas',
                        'danger' => 'belum_lunas',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unit_id')
                    ->label('Unit')
                    ->options(\App\Models\Unit::pluck('nama', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('siswa', fn($q) => $q->where('unit_id', $data['value']));
                        }
                    }),
                Tables\Filters\SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->options(PembayaranSpp::distinct()->pluck('tahun', 'tahun')),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('cetak_laporan')
                    ->label('Cetak Laporan')
                    ->icon('heroicon-o-printer')
                    ->form([
                        \Filament\Forms\Components\Select::make('unit_id')
                             ->label('Unit')
                             ->options(\App\Models\Unit::pluck('nama', 'id')),
                        \Filament\Forms\Components\Select::make('tahun')
                             ->label('Tahun')
                             ->options(PembayaranSpp::distinct()->pluck('tahun', 'tahun')),
                        \Filament\Forms\Components\Select::make('status')
                             ->label('Status Bayar')
                             ->options([
                                 'semua' => 'Semua',
                                 'lunas' => 'Lunas',
                                 'belum_lunas' => 'Belum Lunas',
                             ])
                             ->default('semua'),
                    ])
                    ->action(function (array $data) {
                        return redirect()->route('print.laporan-spp', [
                            'unit_id' => $data['unit_id'] ?? null,
                            'tahun' => $data['tahun'] ?? null,
                            'status' => $data['status'] ?? 'semua',
                        ]);
                    })
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['tendik', 'admin', 'administrator', 'kepsek', 'super_admin']);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['tendik', 'admin', 'administrator', 'kepsek', 'super_admin']);
    }
}
