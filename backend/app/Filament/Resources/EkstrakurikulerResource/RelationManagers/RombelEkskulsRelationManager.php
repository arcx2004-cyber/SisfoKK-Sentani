<?php

namespace App\Filament\Resources\EkstrakurikulerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RombelEkskulsRelationManager extends RelationManager
{
    protected static string $relationship = 'rombelEkskuls';

    protected static ?string $title = 'Anggota Ekstrakurikuler';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('siswa_id')
                    ->label('Siswa')
                    ->relationship('siswa', 'nama_lengkap')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('semester_id')
                    ->relationship('semester', 'tipe') // Just to show label
                    ->label('Semester')
                    ->default(fn() => \App\Models\Semester::where('is_active', true)->first()?->id)
                    ->required()
                    ->visible(false), // Hidden, auto-assigned default
                 Forms\Components\Hidden::make('semester_id')
                    ->default(fn() => \App\Models\Semester::where('is_active', true)->first()?->id),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('siswa.nama_lengkap')
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nis')
                    ->label('NIS')
                    ->searchable(),
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                    ->label('Nama Siswa')
                    ->searchable(),
                Tables\Columns\TextColumn::make('siswa.rombels.nama')
                    ->label('Kelas Saat Ini')
                    ->limit(20),
                Tables\Columns\TextColumn::make('nilaiEkskul.grade')
                    ->label('Grade')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'A' => 'success',
                        'B' => 'info',
                        'C' => 'warning',
                        'D' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Anggota')
                    ->visible(fn () => !auth()->user()->hasRole('wali_kelas')),
            ])
            ->actions([
                Tables\Actions\Action::make('input_nilai')
                    ->label('Input Nilai')
                    ->icon('heroicon-o-pencil-square')
                    ->visible(fn () => !auth()->user()->hasRole('wali_kelas'))
                    ->form([
                        Forms\Components\Select::make('grade')
                            ->label('Grade')
                            ->options([
                                'A' => 'A - Sangat Baik',
                                'B' => 'B - Baik',
                                'C' => 'C - Cukup',
                                'D' => 'D - Kurang',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi')
                            ->rows(3),
                    ])
                    ->action(function ($record, array $data) {
                        $record->nilaiEkskul()->updateOrCreate(
                            ['rombel_ekskul_id' => $record->id],
                            $data
                        );
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('Keluarkan')
                    ->visible(fn () => !auth()->user()->hasRole('wali_kelas')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => !auth()->user()->hasRole('wali_kelas')),
                ]),
            ]);
    }
}
