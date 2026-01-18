<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjekP5Resource\Pages;
use App\Filament\Resources\ProjekP5Resource\RelationManagers;
use App\Models\ProjekP5;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjekP5Resource extends Resource
{
    protected static ?string $model = ProjekP5::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Akademik Projek P5';

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole('super_admin')) {
             return false;
        }
        return auth()->user()->hasAnyRole(['wali_kelas', 'admin', 'administrator', 'kepsek']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Projek')
                    ->schema([
                        Forms\Components\Select::make('unit_id')
                            ->relationship('unit', 'nama')
                            ->required(),
                        Forms\Components\Select::make('tahun_ajaran_id')
                            ->relationship('tahunAjaran', 'nama')
                            ->required(),
                        Forms\Components\Select::make('semester_id')
                            ->relationship('semester', 'tipe')
                            ->required(),
                        Forms\Components\TextInput::make('tema')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('deskripsi')
                            ->columnSpanFull(),
                        Forms\Components\Select::make('fase')
                            ->options([
                                'A' => 'Fase A (Kelas 1-2)',
                                'B' => 'Fase B (Kelas 3-4)',
                                'C' => 'Fase C (Kelas 5-6)',
                                'D' => 'Fase D (SMP)',
                            ])
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Dimensi Profil Pelajar Pancasila')
                    ->schema([
                        Forms\Components\CheckboxList::make('dimensions')
                            ->relationship('dimensions', 'nama')
                            ->columns(1)
                            ->gridDirection('row')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('unit.nama')->sortable(),
                Tables\Columns\TextColumn::make('semester.semester')->label('Semester'),
                Tables\Columns\TextColumn::make('tema')->searchable(),
                Tables\Columns\TextColumn::make('judul')->searchable(),
                Tables\Columns\TextColumn::make('fase'),
                Tables\Columns\TextColumn::make('dimensions_count')->counts('dimensions')->label('Jml Dimensi'),
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
            'index' => Pages\ListProjekP5S::route('/'),
            'create' => Pages\CreateProjekP5::route('/create'),
            'edit' => Pages\EditProjekP5::route('/{record}/edit'),
        ];
    }
}
