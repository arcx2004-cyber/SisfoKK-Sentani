<?php

namespace App\Filament\Resources\KokurikulerResource\RelationManagers;

use App\Models\Semester;
use App\Models\Siswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class AnggotasRelationManager extends RelationManager
{
    protected static string $relationship = 'anggotas';

    protected static ?string $title = 'Anggota Kokurikuler';

    public function form(Form $form): Form
    {
        $kokurikuler = $this->getOwnerRecord();
        
        return $form
            ->schema([
                Forms\Components\Select::make('siswa_id')
                    ->label('Siswa')
                    ->options(function () use ($kokurikuler) {
                        return Siswa::where('unit_id', $kokurikuler->unit_id)
                            ->where('status', 'aktif')
                            ->pluck('nama_lengkap', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Select::make('semester_id')
                    ->label('Semester')
                    ->options(Semester::with('tahunAjaran')->get()->mapWithKeys(function ($semester) {
                        return [$semester->id => $semester->tahunAjaran->nama . ' - ' . ucfirst($semester->tipe)];
                    }))
                    ->default(Semester::where('is_active', true)->first()?->id)
                    ->required()
                    ->visible(false),
                 Forms\Components\Hidden::make('semester_id')
                    ->default(fn() => Semester::where('is_active', true)->first()?->id),
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
                Tables\Columns\TextColumn::make('semester.tahunAjaran.nama')
                    ->label('Tahun Ajaran')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nilai.grade')
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
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Peserta'),
            ])
            ->actions([
                Tables\Actions\Action::make('input_nilai')
                    ->label('Input Nilai')
                    ->icon('heroicon-o-pencil-square')
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
                        $record->nilai()->updateOrCreate(
                            ['anggota_kokurikuler_id' => $record->id],
                            $data
                        );
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
