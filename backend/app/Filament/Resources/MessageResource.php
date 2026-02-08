<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\Models\Message;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Navigation\NavigationGroup;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';
    protected static ?string $navigationGroup = 'CMS';
    protected static ?string $navigationLabel = 'Pesan Masuk';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Pesan')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Pengirim')
                            ->readonly(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->readonly(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor WA')
                            ->readonly(),
                        Forms\Components\TextInput::make('subject')
                            ->label('Subjek')
                            ->columnSpanFull()
                            ->readonly(),
                        Forms\Components\Textarea::make('content')
                            ->label('Isi Pesan')
                            ->rows(5)
                            ->columnSpanFull()
                            ->readonly(),
                        Forms\Components\Toggle::make('is_read')
                            ->label('Tandai Sudah Dibaca')
                            ->onColor('success')
                            ->offColor('danger'),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->label('WA')
                    ->icon('heroicon-o-phone')
                    ->url(fn (Message $record) => $record->phone ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $record->phone) : null)
                    ->openUrlInNewTab(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subjek')
                    ->limit(30)
                    ->searchable(),
                Tables\Columns\ToggleColumn::make('is_read')
                    ->label('Dibaca')
                    ->onColor('success')
                    ->offColor('danger')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label('Status Baca'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Lihat')->modalHeading('Detail Pesan')->icon('heroicon-o-eye'),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_read', false)->count() ?: null;
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
            'index' => Pages\ListMessages::route('/'),
            // 'create' => Pages\CreateMessage::route('/create'), // Disable create
            // 'edit' => Pages\EditMessage::route('/{record}/edit'), // View only mostly
        ];
    }
    
    public static function canCreate(): bool
    {
        return false;
    }
}