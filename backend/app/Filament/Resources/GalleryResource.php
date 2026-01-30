<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryResource\Pages;
use App\Filament\Resources\GalleryResource\RelationManagers;
use App\Models\Gallery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GalleryResource extends BaseResource
{
    protected static ?string $model = Gallery::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationGroup = 'Konten Website';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Galeri';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Album')
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('deskripsi')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('kategori')
                            ->placeholder('Misal: Kegiatan, Fasilitas')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_active')
                            ->required()
                            ->default(true),
                    ]),

                Forms\Components\Section::make('Foto Album')
                    ->schema([
                        Forms\Components\Repeater::make('photos')
                            ->relationship()
                            ->schema([
                                Forms\Components\FileUpload::make('gambar')
                                    ->image()
                                    ->directory('gallery-photos')
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('caption')
                                    ->placeholder('Keterangan foto...')
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('urutan')
                                    ->numeric()
                                    ->default(0)
                                    ->columnSpan(1),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->addActionLabel('Tambah Foto')
                            ->reorderableWithButtons(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('photos_count')
                    ->counts('photos')
                    ->label('Jumlah Foto'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}
