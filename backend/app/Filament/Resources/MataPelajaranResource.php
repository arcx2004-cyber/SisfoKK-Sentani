<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MataPelajaranResource\Pages;
use App\Filament\Resources\MataPelajaranResource\RelationManagers;
use App\Models\MataPelajaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Traits\HasUnitFiltering;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MataPelajaranResource extends BaseResource
{
    use HasUnitFiltering;

    protected static ?string $model = MataPelajaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    
    public static function getNavigationGroup(): ?string
    {
        if (auth()->user()->hasAnyRole(['kepala_sekolah', 'kepsek'])) {
            return 'Administrasi Kepala Sekolah';
        }
        return 'Master Data Sekolah';
    }

    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Mata Pelajaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->relationship('unit', 'nama')
                    ->required(),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kode')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('deskripsi')
                    ->columnSpanFull(),
                Forms\Components\Select::make('jenis')
                    ->label('Jenis Mata Pelajaran')
                    ->options([
                        'wajib' => 'Mata Pelajaran Wajib',
                        'muatan_lokal' => 'Muatan Lokal',
                    ])
                    ->default('wajib')
                    ->required(),
                Forms\Components\TextInput::make('urutan')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('kkm')
                    ->label('KKM')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(75),
                Forms\Components\Select::make('model_penilaian')
                    ->label('Model Penilaian')
                    ->options([
                        'rata_rata' => 'Rata-rata',
                        'bobot' => 'Bobot',
                    ])
                    ->default('rata_rata')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit.nama')
                    ->label('Unit')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jenis')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn ($state) => $state === 'wajib' ? 'primary' : 'warning')
                    ->formatStateUsing(fn ($state) => $state === 'wajib' ? 'Wajib' : 'Mulok'),
                Tables\Columns\TextColumn::make('urutan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kkm')
                    ->label('KKM')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('model_penilaian')
                    ->label('Model Penilaian')
                    ->badge()
                    ->colors([
                        'primary' => 'rata_rata',
                        'secondary' => 'bobot',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'rata_rata' ? 'Rata-rata' : 'Bobot'),
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
            'index' => Pages\ListMataPelajarans::route('/'),
            'create' => Pages\CreateMataPelajaran::route('/create'),
            'edit' => Pages\EditMataPelajaran::route('/{record}/edit'),
        ];
    }
}
