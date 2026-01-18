<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RekapAbsensiResource\Pages;
use App\Filament\Resources\RekapAbsensiResource\RelationManagers;
use App\Models\RekapAbsensi;
use App\Models\Siswa;
use App\Models\Semester;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class RekapAbsensiResource extends Resource
{
    protected static ?string $model = RekapAbsensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    
    protected static ?string $navigationGroup = 'Akademik';
    
    // Label for Wali Kelas "Absensi Siswa"
    protected static ?string $navigationLabel = 'Absensi Siswa';
    
    protected static ?string $modelLabel = 'Rekap Absensi';

    public static function canAccess(): bool
    {
        // Only for Wali Kelas (and Admin)
        return auth()->user()->hasAnyRole(['super_admin', 'admin', 'administrator', 'wali_kelas']);
    }
    
    public static function shouldRegisterNavigation(): bool
    {
        // Only show strictly for Wali Kelas (and Admins)
        // Ensure PTK who are NOT Wali Kelas don't see this.
        $user = auth()->user();

        if ($user->hasRole('super_admin')) {
            return false;
        }

        if ($user->hasRole('ptk') && !$user->hasRole('wali_kelas') && !$user->hasAnyRole(['admin', 'administrator'])) {
            return false;
        }
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user->hasRole('wali_kelas') && $user->guru) {
            $activeSem = Semester::where('is_active', true)->first();
            $wk = \App\Models\WaliKelas::where('guru_id', $user->guru->id)
                ->where('semester_id', $activeSem?->id)
                ->first();

            if ($wk) {
                 // Filter rekap where siswa belongs to rombel
                 $query->whereHas('siswa', function ($q) use ($wk) {
                    $q->whereHas('rombels', function ($q2) use ($wk) {
                        $q2->where('rombels.id', $wk->rombel_id);
                    });
                });
            } else {
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
                        return Siswa::pluck('nama_lengkap', 'id');
                    })
                    ->required()
                    ->searchable()
                    ->preload(),
                
                Forms\Components\Hidden::make('semester_id')
                    ->default(fn() => Semester::where('is_active', true)->first()?->id),

                Forms\Components\TextInput::make('sakit')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('izin')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('alpa')
                    ->required()
                    ->numeric()
                    ->default(0),
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
                Tables\Columns\TextColumn::make('semester.tipe')
                    ->label('Semester'),
                Tables\Columns\TextColumn::make('sakit')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('izin')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('alpa')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_kehadiran')
                    ->label('Total Tidak Hadir')
                    ->state(fn (RekapAbsensi $record) => $record->sakit + $record->izin + $record->alpa),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRekapAbsensis::route('/'),
        ];
    }
}
