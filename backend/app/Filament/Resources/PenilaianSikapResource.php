<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenilaianSikapResource\Pages;
use App\Models\PenilaianSikap;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class PenilaianSikapResource extends Resource
{
    protected static ?string $model = PenilaianSikap::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';
    
    protected static ?string $navigationGroup = 'Administrasi Kelas';
    
    protected static ?string $navigationLabel = 'Penilaian Sikap';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        // Hide for super_admin and wali_kelas (they use batch input page)
        if ($user->hasAnyRole(['super_admin', 'wali_kelas'])) {
            return false;
        }
        return parent::shouldRegisterNavigation();
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['super_admin', 'admin', 'administrator', 'wali_kelas']);
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user->hasRole('wali_kelas') && $user->guru) {
            // Get Active Semester
            $activeSem = Semester::where('is_active', true)->first();
            
            // Find Rombel for this Wali Kelas in Active Semester
            $wk = \App\Models\WaliKelas::where('guru_id', $user->guru->id)
                ->where('semester_id', $activeSem?->id)
                ->first();

            if ($wk) {
                $query->where('rombel_id', $wk->rombel_id);
            } else {
                // Return empty if no assignment found
                $query->whereRaw('1 = 0'); 
            }
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('siswa_id')
                    ->label('Siswa')
                    ->options(function () {
                        $user = Auth::user();
                        if ($user->hasRole('wali_kelas') && $user->guru) {
                             $activeSem = Semester::where('is_active', true)->first();
                             $wk = \App\Models\WaliKelas::where('guru_id', $user->guru->id)
                                ->where('semester_id', $activeSem?->id)
                                ->first();
                             
                             if ($wk) {
                                return Siswa::whereHas('rombels', function ($q) use ($wk) {
                                    $q->where('rombels.id', $wk->rombel_id);
                                })->pluck('nama_lengkap', 'id');
                             }
                        }
                        // Fallback for Admin: Show all active students maybe? Or just all.
                        return Siswa::pluck('nama_lengkap', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->preload(),
                
                Forms\Components\Hidden::make('rombel_id')
                    ->default(function () {
                        $user = Auth::user();
                        if ($user->hasRole('wali_kelas') && $user->guru) {
                             $activeSem = Semester::where('is_active', true)->first();
                             $wk = \App\Models\WaliKelas::where('guru_id', $user->guru->id)
                                ->where('semester_id', $activeSem?->id)
                                ->first();
                             return $wk?->rombel_id;
                        }
                        return null;
                    }),

                Forms\Components\Hidden::make('tahun_ajaran_id')
                    ->default(fn() => TahunAjaran::where('is_active', true)->first()?->id),

                Forms\Components\Hidden::make('semester_id')
                    ->default(fn() => Semester::where('is_active', true)->first()?->id),

                Forms\Components\Section::make('Nilai Sikap')
                    ->schema([
                        Forms\Components\Select::make('kedisiplinan')
                            ->options(self::getSikapOptions())
                            ->required(),
                        Forms\Components\Select::make('kejujuran')
                            ->options(self::getSikapOptions())
                            ->required(),
                        Forms\Components\Select::make('kesopanan')
                            ->options(self::getSikapOptions())
                            ->required(),
                        Forms\Components\Select::make('kebersihan')
                            ->options(self::getSikapOptions())
                            ->required(),
                        Forms\Components\Select::make('kepedulian')
                            ->options(self::getSikapOptions())
                            ->required(),
                        Forms\Components\Select::make('tanggung_jawab')
                            ->options(self::getSikapOptions())
                            ->required(),
                        Forms\Components\Select::make('percaya_diri')
                            ->options(self::getSikapOptions())
                            ->required(),
                    ])->columns(2),
            ]);
    }
    
    protected static function getSikapOptions(): array {
        return [
            'A' => 'A',
            'B' => 'B',
            'C' => 'C',
            'D' => 'D',
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                    ->label('Nama Siswa')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('kedisiplinan'),
                Tables\Columns\TextColumn::make('kejujuran'),
                Tables\Columns\TextColumn::make('tanggung_jawab'),
                Tables\Columns\TextColumn::make('semester.tipe')
                    ->label('Semester'),
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
            'index' => Pages\ListPenilaianSikaps::route('/'),
            'create' => Pages\CreatePenilaianSikap::route('/create'),
            'edit' => Pages\EditPenilaianSikap::route('/{record}/edit'),
        ];
    }
}
