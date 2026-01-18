<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TarifKegiatanResource\Pages;
use App\Models\TarifKegiatan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TarifKegiatanResource extends BaseResource
{
    protected static ?string $model = TarifKegiatan::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Tarif Kegiatan';

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
                Forms\Components\TextInput::make('nama_kegiatan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nominal')
                    ->required()
                    ->numeric()
                    ->prefix('Rp'),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit.nama')->label('Unit')->sortable(),
                Tables\Columns\TextColumn::make('tahunAjaran.nama')->label('Tahun Ajaran')->sortable(),
                Tables\Columns\TextColumn::make('nama_kegiatan')->searchable(),
                Tables\Columns\TextColumn::make('nominal')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('keterangan')->limit(30),
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
                    ->action(function (TarifKegiatan $record) {
                        $students = \App\Models\Siswa::where('unit_id', $record->unit_id)
                            ->where('status', 'aktif')
                            ->get();

                        $count = 0;
                        foreach ($students as $siswa) {
                            \App\Models\PembayaranKegiatan::firstOrCreate(
                                [
                                    'siswa_id' => $siswa->id,
                                    'tarif_kegiatan_id' => $record->id,
                                ],
                                [
                                    'nominal' => $record->nominal,
                                    'nominal_bayar' => 0,
                                    'status' => 'belum_bayar',
                                ]
                            );
                            $count++;
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Tagihan Berhasil Dibuat')
                            ->body("Tagihan Kegiatan berhasil dibuat untuk {$count} siswa.")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Generate Tagihan Kegiatan')
                    ->modalDescription('Apakah Anda yakin ingin membuat tagihan untuk semua siswa aktif di unit ini? Siswa yang sudah memiliki tagihan ini tidak akan diduplikasi.'),
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
            'index' => Pages\ListTarifKegiatans::route('/'),
            'create' => Pages\CreateTarifKegiatan::route('/create'),
            'edit' => Pages\EditTarifKegiatan::route('/{record}/edit'),
        ];
    }
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'administrator', 'kepsek', 'super_admin']);
    }
}
