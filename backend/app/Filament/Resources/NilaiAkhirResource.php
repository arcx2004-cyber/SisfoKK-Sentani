<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NilaiAkhirResource\Pages;
use App\Filament\Resources\NilaiAkhirResource\RelationManagers;
use App\Models\NilaiAkhir;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NilaiAkhirResource extends BaseResource
{
    protected static ?string $model = NilaiAkhir::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    public static function getNavigationGroup(): ?string
    {
        return auth()->user()->hasRole('ptk') ? 'Akademik Guru' : 'Akademik';
    }
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Nilai Akhir';

    public static function shouldRegisterNavigation(): bool
    {
        return !auth()->user()->hasAnyRole(['ptk', 'wali_kelas', 'super_admin']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('siswa_id')
                    ->relationship('siswa', 'id')
                    ->required(),
                Forms\Components\Select::make('mata_pelajaran_id')
                    ->relationship('mataPelajaran', 'id')
                    ->required(),
                Forms\Components\Select::make('semester_id')
                    ->relationship('semester', 'id')
                    ->required(),
                Forms\Components\TextInput::make('nilai')
                    ->numeric(),
                Forms\Components\Textarea::make('deskripsi_capaian')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mataPelajaran.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('semester.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nilai')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListNilaiAkhirs::route('/'),
            'create' => Pages\CreateNilaiAkhir::route('/create'),
            'edit' => Pages\EditNilaiAkhir::route('/{record}/edit'),
        ];
    }
}
