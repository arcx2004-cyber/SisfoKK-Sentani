<?php

namespace App\Filament\Resources\RapbsResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DetailsRelationManager extends RelationManager
{
    protected static string $relationship = 'details';
    protected static ?string $title = 'Rincian Anggaran';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('jenis')
                    ->options([
                        'pendapatan' => 'Pendapatan',
                        'pengeluaran' => 'Pengeluaran',
                    ])
                    ->live() // Make it reactive
                    ->required(),
                Forms\Components\Select::make('sumber_dana')
                    ->options([
                        'bosp' => 'Dana BOSP',
                        'kegiatan' => 'Dana Kegiatan Tahunan',
                    ])
                    ->required(fn (Forms\Get $get) => $get('jenis') === 'pengeluaran') // Required if Expense
                    ->visible(fn (Forms\Get $get) => $get('jenis') === 'pengeluaran') // Visible only for Expense? Or both? User said "menentukan pengeluaran ... diambil dari Dana yang mana". Let's show for both but enforce for expense.
                    ->label('Sumber Dana'),
                Forms\Components\TextInput::make('uraian')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nominal')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('uraian')
            ->columns([
                Tables\Columns\BadgeColumn::make('jenis')
                    ->colors([
                        'success' => 'pendapatan',
                        'danger' => 'pengeluaran',
                    ]),
                Tables\Columns\TextColumn::make('sumber_dana')
                    ->badge()
                    ->colors([
                        'warning' => 'kegiatan',
                        'info' => 'bosp',
                    ])
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'bosp' => 'BOSP',
                        'kegiatan' => 'Kegiatan',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('uraian')->searchable(),
                Tables\Columns\TextColumn::make('nominal')->money('IDR')->sortable(),
                Tables\Columns\TextColumn::make('keterangan')->limit(30),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('jenis')
                    ->options([
                        'pendapatan' => 'Pendapatan',
                        'pengeluaran' => 'Pengeluaran',
                    ]),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Item')
                    ->after(fn ($livewire) => $livewire->getOwnerRecord()->calculateTotals()),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->after(fn ($livewire) => $livewire->getOwnerRecord()->calculateTotals()),
                Tables\Actions\DeleteAction::make()
                    ->after(fn ($livewire) => $livewire->getOwnerRecord()->calculateTotals()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(fn ($livewire) => $livewire->getOwnerRecord()->calculateTotals()),
                ]),
            ]);
    }
}
