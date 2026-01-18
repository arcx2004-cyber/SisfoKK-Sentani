<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PpdbSettingResource\Pages;
use App\Models\PpdbSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PpdbSettingResource extends BaseResource
{
    protected static ?string $model = PpdbSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'PPDB';
    protected static ?int $navigationSort = 0;
    protected static ?string $navigationLabel = 'Pengaturan PPDB';
    protected static ?string $modelLabel = 'Pengaturan PPDB';
    protected static ?string $pluralModelLabel = 'Pengaturan PPDB';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Status PPDB')
                    ->description('Kontrol status buka/tutup pendaftaran')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Status PPDB')
                            ->helperText('Aktifkan untuk membuka pendaftaran PPDB')
                            ->onIcon('heroicon-m-check')
                            ->offIcon('heroicon-m-x-mark')
                            ->onColor('success')
                            ->offColor('danger')
                            ->required(),
                    ])->columns(1),

                Forms\Components\Section::make('Periode Pendaftaran')
                    ->schema([
                        Forms\Components\DatePicker::make('tanggal_buka')
                            ->label('Tanggal Buka Pendaftaran')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y'),
                        Forms\Components\DatePicker::make('tanggal_tutup')
                            ->label('Tanggal Tutup Pendaftaran')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->afterOrEqual('tanggal_buka'),
                    ])->columns(2),

                Forms\Components\Section::make('Unit & Tahun Ajaran')
                    ->schema([
                        Forms\Components\Select::make('unit_id')
                            ->relationship('unit', 'nama')
                            ->label('Unit Sekolah')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('tahun_ajaran_id')
                            ->relationship('tahunAjaran', 'nama')
                            ->label('Tahun Ajaran')
                            ->required()
                            ->searchable()
                            ->preload(),
                    ])->columns(2),

                Forms\Components\Section::make('Informasi Pendaftaran')
                    ->schema([
                        Forms\Components\TextInput::make('biaya_pendaftaran')
                            ->label('Biaya Pendaftaran')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        Forms\Components\RichEditor::make('alur_pendaftaran')
                            ->label('Alur Pendaftaran')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('persyaratan')
                            ->label('Persyaratan Pendaftaran')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit.nama')
                    ->label('Unit')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tahunAjaran.nama')
                    ->label('Tahun Ajaran')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('tanggal_buka')
                    ->label('Tanggal Buka')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_tutup')
                    ->label('Tanggal Tutup')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_pendaftaran')
                    ->label('Biaya')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('pendaftarans_count')
                    ->counts('pendaftarans')
                    ->label('Pendaftar'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('unit')
                    ->relationship('unit', 'nama'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status PPDB'),
            ])
            ->actions([
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
            'index' => Pages\ListPpdbSettings::route('/'),
            'create' => Pages\CreatePpdbSetting::route('/create'),
            'edit' => Pages\EditPpdbSetting::route('/{record}/edit'),
        ];
    }
}
