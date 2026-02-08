<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MadingResource\Pages;
use App\Models\Mading;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;

class MadingResource extends BaseResource
{
    protected static ?string $model = Mading::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationGroup = 'Kesiswaan';
    protected static ?string $navigationLabel = 'Mading Digital';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Judul Karya')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('author_name')
                    ->label('Nama Pengkarya')
                    ->required()
                    ->maxLength(255),
                Forms\Components\RichEditor::make('content')
                    ->label('Isi Mading')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->label('Gambar Utama')
                    ->image()
                    ->directory('mading'),
                Forms\Components\Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'rejected' => 'Rejected',
                    ])
                    ->default('draft')
                    ->required(),
                Forms\Components\DateTimePicker::make('published_at')
                    ->label('Tanggal Terbit'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('author_name')
                    ->label('Pengkarya')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'rejected' => 'danger',
                        'draft' => 'warning',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Tanggal Terbit')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'rejected' => 'Rejected',
                    ]),
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
            'index' => Pages\ListMadings::route('/'),
            'create' => Pages\CreateMading::route('/create'),
            'edit' => Pages\EditMading::route('/{record}/edit'),
        ];
    }
}
