<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembayaranKegiatanResource\Pages;
use App\Models\PembayaranKegiatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Siswa;
use App\Models\TarifKegiatan;
use Illuminate\Database\Eloquent\Builder;

class PembayaranKegiatanResource extends BaseResource
{
    protected static ?string $model = PembayaranKegiatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Pembayaran Kegiatan';

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
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('tarif_kegiatan_id', null)),
                
                Forms\Components\Select::make('tarif_kegiatan_id')
                    ->label('Kegiatan')
                    ->options(function (Forms\Get $get) {
                        $siswaId = $get('siswa_id');
                        if (!$siswaId) return [];
                        $siswa = Siswa::find($siswaId);
                        if (!$siswa) return [];
                        
                        return TarifKegiatan::where('unit_id', $siswa->unit_id)
                            ->where('tahun_ajaran_id', \App\Models\TahunAjaran::getActive()->id)
                            ->get()
                            ->pluck('nama_kegiatan', 'id');
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                         $tarif = TarifKegiatan::find($state);
                         if ($tarif) {
                             $set('nominal', $tarif->nominal);
                             $set('nominal_bayar', $tarif->nominal);
                         }
                    }),

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
                    
                Forms\Components\Textarea::make('keterangan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('tarifKegiatan.nama_kegiatan')->label('Kegiatan'),
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
                            ->default('tunai')
                            ->required(),
                        Forms\Components\DatePicker::make('tanggal_bayar')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (PembayaranKegiatan $record, array $data) {
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
                    ->visible(fn (PembayaranKegiatan $record) => $record->status === 'belum_lunas'),
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembayaranKegiatans::route('/'),
            'create' => Pages\CreatePembayaranKegiatan::route('/create'),
            'edit' => Pages\EditPembayaranKegiatan::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['tendik', 'admin', 'administrator', 'kepsek', 'super_admin']);
    }
}
