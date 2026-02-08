<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrestasiResource\Pages;
use App\Models\Prestasi;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class PrestasiResource extends BaseResource
{
    protected static ?string $model = Prestasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Kesiswaan';
    protected static ?string $navigationLabel = 'Prestasi Siswa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('siswa_id')
                    ->relationship('siswa', 'nama_lengkap')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('semester_id')
                    ->relationship('semester', 'nama')
                    ->required(),
                Forms\Components\TextInput::make('judul')
                    ->label('Judul Prestasi')
                    ->placeholder('Contoh: Juara 1 Lomba Matematika')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal Perolehan'),
                Forms\Components\TextInput::make('jenis')
                    ->label('Tingkat/Jenis')
                    ->placeholder('Contoh: Nasional / Akademik'),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('foto')
                    ->directory('prestasi')
                    ->image(),
                Forms\Components\Toggle::make('is_public')
                    ->label('Tampilkan di Website?')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                    ->label('Siswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('judul')
                    ->label('Prestasi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Tingkat'),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_public')
                    ->boolean(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListPrestasis::route('/'),
            'create' => Pages\CreatePrestasi::route('/create'),
            'edit' => Pages\EditPrestasi::route('/{record}/edit'),
        ];
    }
}
