<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TujuanPembelajaranResource\Pages;
use App\Filament\Resources\TujuanPembelajaranResource\RelationManagers;
use App\Models\TujuanPembelajaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TujuanPembelajaranResource extends Resource
{
    protected static ?string $model = TujuanPembelajaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Akademik Projek P5';
    
    public static function shouldRegisterNavigation(): bool
    {
        return !auth()->user()->hasAnyRole(['siswa', 'ptk', 'super_admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('capaian_pembelajaran_id')
                    ->relationship('capaianPembelajaran', 'kode')
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->kode} - {$record->deskripsi}")
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\TextInput::make('kode')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('deskripsi')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('urutan')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('capaianPembelajaran.kode')->sortable(),
                Tables\Columns\TextColumn::make('kode')->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')->limit(50),
                Tables\Columns\TextColumn::make('urutan')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListTujuanPembelajarans::route('/'),
            'create' => Pages\CreateTujuanPembelajaran::route('/create'),
            'edit' => Pages\EditTujuanPembelajaran::route('/{record}/edit'),
        ];
    }
}
