<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TarifSppResource\Pages;
use App\Filament\Resources\TarifSppResource\RelationManagers;
use App\Models\TarifSpp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TarifSppResource extends BaseResource
{
    protected static ?string $model = TarifSpp::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Tarif SPP';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'nama')
                    ->required(),
                Forms\Components\Select::make('tahun_ajaran_id')
                    ->relationship('tahunAjaran', 'nama')
                    ->required(),
                Forms\Components\TextInput::make('nominal')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit.nama')
                    ->label('Unit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tahunAjaran.nama')
                    ->label('Tahun Ajaran')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nominal')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('keterangan')
                    ->limit(50),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unit_id')
                    ->relationship('unit', 'nama')
                    ->label('Unit'),
                Tables\Filters\SelectFilter::make('tahun_ajaran_id')
                    ->relationship('tahunAjaran', 'nama')
                    ->label('Tahun Ajaran'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('generate_tagihan')
                    ->label('Generate Tagihan')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->form([
                        Forms\Components\Select::make('bulan_awal')
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ])
                            ->default(7) // July default start of academic year
                            ->required(),
                        Forms\Components\Select::make('bulan_akhir')
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                            ])
                            ->default(6) // June default end
                            ->required(),
                        Forms\Components\TextInput::make('tahun')
                            ->numeric()
                            ->default(date('Y'))
                            ->required()
                            ->helperText('Tahun untuk bulan awal. Sistem akan otomatis menyesuaikan tahun untuk bulan setelah Desember.'),
                    ])
                    ->action(function (TarifSpp $record, array $data) {
                        $students = \App\Models\Siswa::where('unit_id', $record->unit_id)
                            ->where('status', 'aktif')
                            ->get();

                        $bulanAwal = (int)$data['bulan_awal'];
                        $bulanAkhir = (int)$data['bulan_akhir'];
                        $tahunAwal = (int)$data['tahun'];

                        // Normalized month list for academic year ordering if needed, but simple loop works
                        // Logic: Loop from bulanAwal to bulanAkhir, handling year rollover
                        
                        $count = 0;
                        
                        foreach ($students as $siswa) {
                            $currentBulan = $bulanAwal;
                            $currentTahun = $tahunAwal;
                            
                            // Prevent infinite loop if range is weird, simpler approach:
                            // If bulanAkhir < bulanAwal, assume it crosses year
                            
                            $monthsToProcess = [];
                            if ($bulanAkhir >= $bulanAwal) {
                                for ($m = $bulanAwal; $m <= $bulanAkhir; $m++) {
                                    $monthsToProcess[] = ['m' => $m, 'y' => $currentTahun];
                                }
                            } else {
                                // Cross year (e.g. July to June)
                                for ($m = $bulanAwal; $m <= 12; $m++) {
                                    $monthsToProcess[] = ['m' => $m, 'y' => $currentTahun];
                                }
                                for ($m = 1; $m <= $bulanAkhir; $m++) {
                                    $monthsToProcess[] = ['m' => $m, 'y' => $currentTahun + 1];
                                }
                            }

                            foreach ($monthsToProcess as $date) {
                                \App\Models\PembayaranSpp::firstOrCreate(
                                    [
                                        'siswa_id' => $siswa->id,
                                        'tarif_spp_id' => $record->id,
                                        'bulan' => $date['m'],
                                        'tahun' => $date['y'],
                                    ],
                                    [
                                        'nominal' => $record->nominal,
                                        'nominal_bayar' => 0,
                                        'status' => 'belum_bayar',
                                    ]
                                );
                                $count++;
                            }
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Tagihan Berhasil Dibuat')
                            ->body("Tagihan SPP berhasil dibuat untuk {$students->count()} siswa.")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),
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
            'index' => Pages\ListTarifSpps::route('/'),
            'create' => Pages\CreateTarifSpp::route('/create'),
            'edit' => Pages\EditTarifSpp::route('/{record}/edit'),
        ];
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasRole(['super_admin']);
    }
}
