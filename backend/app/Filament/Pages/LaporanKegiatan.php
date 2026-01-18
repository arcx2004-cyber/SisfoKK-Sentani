<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;
use App\Models\PembayaranKegiatan;
use Illuminate\Database\Eloquent\Builder;

class LaporanKegiatan extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Laporan Pembayaran';
    protected static ?string $navigationLabel = 'Laporan Uang Kegiatan';
    protected static ?int $navigationSort = 2;

    protected static string $view = 'filament.pages.laporan-kegiatan';

    public function table(Table $table): Table
    {
        return $table
            ->query(PembayaranKegiatan::query()->with(['siswa', 'siswa.unit', 'siswa.rombels', 'tarifKegiatan']))
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                    ->label('Siswa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas_siswa')
                    ->label('Kelas')
                    ->state(fn (PembayaranKegiatan $record) => $record->siswa?->getCurrentRombel()?->nama ?? '-'),
                Tables\Columns\TextColumn::make('tarifKegiatan.nama_kegiatan')
                    ->label('Kegiatan')
                    ->searchable(),
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
                        return redirect()->route('print.laporan-kegiatan', [
                            'unit_id' => $data['unit_id'] ?? null,
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
