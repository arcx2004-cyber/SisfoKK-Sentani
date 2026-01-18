<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembayaranSppResource\Pages;
use App\Filament\Resources\PembayaranSppResource\RelationManagers;
use App\Models\PembayaranSpp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\TarifSpp;
use App\Models\Siswa;
use Filament\Forms\Get;
use Filament\Forms\Set;

class PembayaranSppResource extends BaseResource
{
    protected static ?string $model = PembayaranSpp::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Keuangan';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('siswa_id')
                    ->label('Siswa')
                    ->options(Siswa::pluck('nama_lengkap', 'id'))
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('tarif_spp_id', null)),
                
                Forms\Components\Select::make('tarif_spp_id')
                    ->label('Tarif SPP')
                    ->options(function (Get $get) {
                        $siswaId = $get('siswa_id');
                        if (!$siswaId) return [];
                        $siswa = Siswa::find($siswaId);
                        if (!$siswa) return [];
                        
                        return TarifSpp::where('unit_id', $siswa->unit_id)
                            ->where('tahun_ajaran_id', \App\Models\TahunAjaran::getActive()->id)
                            ->get()
                            ->mapWithKeys(fn ($tarif) => [$tarif->id => "Rp " . number_format($tarif->nominal, 0, ',', '.')]);
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Set $set) {
                         $tarif = TarifSpp::find($state);
                         if ($tarif) {
                             $set('nominal', $tarif->nominal);
                             $set('nominal_bayar', $tarif->nominal);
                         }
                    }),

                Forms\Components\Select::make('bulan')
                    ->options([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ])
                    ->required(),
                
                Forms\Components\TextInput::make('tahun')
                    ->numeric()
                    ->default(date('Y'))
                    ->required(),
                
                Forms\Components\TextInput::make('nominal')
                    ->readOnly()
                    ->prefix('Rp'),
                
                Forms\Components\TextInput::make('nominal_bayar')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                
                Forms\Components\DatePicker::make('tanggal_bayar')
                    ->default(now())
                    ->required(),
                
                Forms\Components\Select::make('status')
                    ->options([
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',
                    ])
                    ->default('lunas')
                    ->required(),
                
                Forms\Components\Select::make('metode_pembayaran')
                    ->options([
                        'tunai' => 'Tunai',
                        'transfer' => 'Transfer',
                    ])
                    ->default('tunai'),
                    
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('siswa.rombel.nama')->label('Kelas')->sortable(),
                Tables\Columns\TextColumn::make('bulan')
                    ->formatStateUsing(fn (string $state): string => match ((int)$state) {
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                        default => $state,
                    })->sortable(),
                Tables\Columns\TextColumn::make('tahun')->sortable(),
                Tables\Columns\TextColumn::make('nominal_bayar')->money('IDR'),
                Tables\Columns\TextColumn::make('tanggal_bayar')->date(),
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
                            $query->whereHas('siswa', function ($q) use ($data) {
                                $q->where('unit_id', $data['value']);
                            });
                        }
                    }),
                Tables\Filters\SelectFilter::make('rombel_id')
                    ->label('Kelas')
                    ->options(\App\Models\Rombel::pluck('nama', 'id'))
                    ->query(function (Builder $query, array $data) {
                        if (!empty($data['value'])) {
                            $query->whereHas('siswa', function ($q) use ($data) {
                                $q->whereHas('rombels', function ($rq) use ($data) {
                                    $rq->where('rombels.id', $data['value']);
                                });
                            });
                        }
                    }),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('bayar')
                    ->label('Bayar')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran')
                    ->modalDescription('Apakah anda yakin ingin mengubah status menjadi Lunas?')
                    ->form([
                        Forms\Components\Select::make('metode_pembayaran')
                            ->options([
                                'tunai' => 'Tunai',
                                'transfer' => 'Transfer',
                            ])
                            ->default('transfer')
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_bayar')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (PembayaranSpp $record, array $data) {
                        $record->update([
                            'status' => 'lunas',
                            'nominal_bayar' => $record->nominal, // Assume full payment
                            'metode_pembayaran' => $data['metode_pembayaran'],
                            'tanggal_bayar' => $data['tanggal_bayar'],
                        ]);
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Pembayaran Berhasil Dicatat')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (PembayaranSpp $record) => $record->status === 'belum_lunas'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembayaranSpps::route('/'),
            'create' => Pages\CreatePembayaranSpp::route('/create'),
            'edit' => Pages\EditPembayaranSpp::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['tendik', 'super_admin']);
    }
}
