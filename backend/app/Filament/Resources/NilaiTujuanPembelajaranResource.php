<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NilaiTujuanPembelajaranResource\Pages;
use App\Filament\Resources\NilaiTujuanPembelajaranResource\RelationManagers;
use App\Models\NilaiTujuanPembelajaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NilaiTujuanPembelajaranResource extends BaseResource
{
    protected static ?string $model = NilaiTujuanPembelajaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    public static function getNavigationGroup(): ?string
    {
        return auth()->user()->hasRole('ptk') ? 'Akademik Guru' : 'Akademik';
    }
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return auth()->user()->hasRole('ptk') ? 'Input Nilai' : 'Nilai Sumatif';
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getModelLabel(): string
    {
        return 'Nilai Sumatif Harian';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->options(fn () => \App\Models\JadwalGuru::where('guru_id', auth()->user()->guru?->id)
                        ->with('mataPelajaran')
                        ->get()
                        ->pluck('mataPelajaran.nama', 'mata_pelajaran_id'))
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('tujuan_pembelajaran_id', null))
                    ->dehydrated(false)
                    ->required(),

                Forms\Components\Select::make('rombel_id')
                    ->label('Kelas / Rombel')
                    ->options(fn (Forms\Get $get) => \App\Models\JadwalGuru::where('guru_id', auth()->user()->guru?->id)
                        ->where('mata_pelajaran_id', $get('mata_pelajaran_id'))
                        ->with('rombel')
                        ->get()
                        ->pluck('rombel.nama', 'rombel_id'))
                    ->live()
                    ->afterStateUpdated(fn (Forms\Set $set) => $set('siswa_id', null))
                    ->dehydrated(false)
                    ->required(),

                Forms\Components\Select::make('tujuan_pembelajaran_id')
                    ->label('Tujuan Pembelajaran')
                    ->options(fn (Forms\Get $get) => \App\Models\TujuanPembelajaran::whereHas('capaianPembelajaran', function ($query) use ($get) {
                            $query->where('mata_pelajaran_id', $get('mata_pelajaran_id'));
                        })->pluck('kode', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\Select::make('siswa_id')
                    ->label('Siswa')
                    ->options(fn (Forms\Get $get) => \App\Models\Siswa::whereHas('anggotaRombels', function ($query) use ($get) {
                            $query->where('rombel_id', $get('rombel_id'));
                        })->pluck('nama_lengkap', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('nilai')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\Textarea::make('deskripsi')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                    ->label('Siswa')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tujuanPembelajaran.capaianPembelajaran.mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tujuanPembelajaran.kode')
                    ->label('Kode TP')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nilai')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListNilaiTujuanPembelajarans::route('/'),
            'create' => Pages\CreateNilaiTujuanPembelajaran::route('/create'),
            'edit' => Pages\EditNilaiTujuanPembelajaran::route('/{record}/edit'),
        ];
    }
}
