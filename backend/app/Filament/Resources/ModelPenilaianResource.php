<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ModelPenilaianResource\Pages;
use App\Models\ModelPenilaian;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ModelPenilaianResource extends Resource
{
    protected static ?string $model = ModelPenilaian::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function getNavigationGroup(): ?string
    {
        if (auth()->user()->hasAnyRole(['kepala_sekolah', 'kepsek'])) {
            return 'Administrasi Kepala Sekolah';
        }
        return 'Master Data Sekolah';
    }

    protected static ?string $navigationLabel = 'Model Penilaian';

    protected static ?string $modelLabel = 'Model Penilaian';

    protected static ?string $pluralModelLabel = 'Model Penilaian';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Model Penilaian')
                    ->schema([
                        Forms\Components\TextInput::make('nama')
                            ->label('Nama Model Penilaian')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Sumatif Harian'),
                        Forms\Components\TextInput::make('kode')
                            ->label('Kode')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(10)
                            ->placeholder('Contoh: SH'),
                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->maxLength(65535),
                        Forms\Components\TextInput::make('urutan')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(50),
                Tables\Columns\TextColumn::make('urutan')
                    ->label('Urutan')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('urutan');
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
            'index' => Pages\ListModelPenilaians::route('/'),
            'create' => Pages\CreateModelPenilaian::route('/create'),
            'edit' => Pages\EditModelPenilaian::route('/{record}/edit'),
        ];
    }
}
