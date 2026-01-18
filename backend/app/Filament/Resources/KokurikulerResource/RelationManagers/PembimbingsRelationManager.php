<?php

namespace App\Filament\Resources\KokurikulerResource\RelationManagers;

use App\Models\Guru;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PembimbingsRelationManager extends RelationManager
{
    protected static string $relationship = 'pembimbings';

    protected static ?string $title = 'Pembimbing';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('guru_id')
                    ->label('Guru Pembimbing')
                    ->options(Guru::pluck('nama_lengkap', 'id'))
                    ->searchable()
                    ->nullable()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $guru = Guru::find($state);
                            if ($guru) {
                                $set('nama_pembimbing', $guru->nama_lengkap);
                                $set('no_telepon', $guru->no_telepon);
                            }
                        }
                    }),
                Forms\Components\TextInput::make('nama_pembimbing')
                    ->label('Nama Pembimbing')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_telepon')
                    ->label('No. Telepon')
                    ->tel()
                    ->maxLength(20),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nama_pembimbing')
            ->columns([
                Tables\Columns\TextColumn::make('guru.nama_lengkap')
                    ->label('Guru')
                    ->placeholder('External'),
                Tables\Columns\TextColumn::make('nama_pembimbing')
                    ->label('Nama Pembimbing'),
                Tables\Columns\TextColumn::make('no_telepon')
                    ->label('No. Telepon'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
}
