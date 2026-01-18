<?php

namespace App\Filament\Resources;

use App\Filament\Resources\JadwalGuruResource\Pages;
use App\Models\JadwalGuru;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class JadwalGuruResource extends BaseResource
{
    protected static ?string $model = JadwalGuru::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    public static function getNavigationGroup(): ?string
    {
        return auth()->user()->hasRole('ptk') ? 'Akademik Guru' : 'Akademik';
    }
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationLabel = 'Jadwal Mata Pelajaran';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
            return false;
        }
        // Hide for PTK regarding of dashboard availability, but allow for admins
        if ($user->hasRole('ptk') && !$user->hasAnyRole(['super_admin', 'admin', 'administrator', 'kepsek'])) {
            return false;
        }
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('rombel_id')
                    ->relationship('rombel', 'nama')
                    ->label('Rombongan Belajar')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('mata_pelajaran_id', null))
                    ->required(),
                Forms\Components\Select::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->options(function (Forms\Get $get) {
                        $rombelId = $get('rombel_id');
                        if (!$rombelId) {
                            return \App\Models\MataPelajaran::all()->pluck('nama', 'id');
                        }
                        $rombel = \App\Models\Rombel::find($rombelId);
                        if (!$rombel) {
                            return [];
                        }
                        return \App\Models\MataPelajaran::where('unit_id', $rombel->unit_id)
                            ->pluck('nama', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('guru_id')
                    ->relationship('guru', 'nama_lengkap')
                    ->label('Guru Pengajar')
                    ->searchable()
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
                    ])
                    ->required(),
                Forms\Components\Select::make('ruang_kelas_id')
                    ->relationship('ruangKelas', 'nama')
                    ->label('Ruang Kelas (Opsional)')
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('semester_id')
                    ->relationship('semester', 'tipe')
                    ->label('Semester')
                    ->required(),
                Forms\Components\TimePicker::make('jam_mulai')
                    ->required()
                    ->seconds(false),
                Forms\Components\TimePicker::make('jam_selesai')
                    ->required()
                    ->seconds(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();
                
                // Filter for Wali Kelas: Only show their Rombel's schedule
                if ($user->hasRole('wali_kelas') && !$user->hasAnyRole(['super_admin', 'admin', 'administrator'])) {
                    $activeSem = \App\Models\Semester::where('is_active', true)->first();
                    $guruId = $user->guru?->id;
                    
                    if ($guruId && $activeSem) {
                        $wk = \App\Models\WaliKelas::where('guru_id', $guruId)
                            ->where('semester_id', $activeSem->id)
                            ->first();

                        if ($wk) {
                            $query->where('rombel_id', $wk->rombel_id)
                                  ->where('semester_id', $activeSem->id);
                            return;
                        }
                    }
                    // If no rombel assigned, show nothing (strict)
                    $query->whereRaw('1 = 0');
                    return;
                }

                // Filter for regular PTK (not Admin/Kepsek/Wali Kelas)
                if ($user->hasRole('ptk') && !$user->hasAnyRole(['super_admin', 'admin', 'administrator', 'kepsek'])) {
                    $guruId = $user->guru?->id;
                    if ($guruId) {
                        $query->where('guru_id', $guruId);
                    }
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('rombel.nama')
                    ->label('Rombel')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('hari')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_mulai')
                    ->time('H:i')
                    ->label('Mulai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jam_selesai')
                    ->time('H:i')
                    ->label('Selesai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('guru.nama_lengkap')
                    ->label('Guru')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('ruangKelas.nama')
                    ->label('Ruang')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('hari', 'asc')
            ->filters([
                SelectFilter::make('rombel_id')
                    ->label('Filter Rombel')
                    ->relationship('rombel', 'nama')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('guru_id')
                    ->label('Filter Guru')
                    ->relationship('guru', 'nama_lengkap')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('hari')
                    ->options([
                        'Senin' => 'Senin',
                        'Selasa' => 'Selasa',
                        'Rabu' => 'Rabu',
                        'Kamis' => 'Kamis',
                        'Jumat' => 'Jumat',
                        'Sabtu' => 'Sabtu',
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJadwalGurus::route('/'),
            'create' => Pages\CreateJadwalGuru::route('/create'),
            'edit' => Pages\EditJadwalGuru::route('/{record}/edit'),
        ];
    }
}
