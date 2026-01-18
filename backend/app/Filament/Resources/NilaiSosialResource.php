<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NilaiSosialResource\Pages;
use App\Models\NilaiSosial;
use App\Models\Siswa;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class NilaiSosialResource extends Resource
{
    protected static ?string $model = NilaiSosial::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationGroup = 'Administrasi Kelas';
    
    protected static ?string $navigationLabel = 'Penilaian Sosial';

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
            $activeSem = Semester::where('is_active', true)->first();
            $wk = \App\Models\WaliKelas::where('guru_id', $user->guru->id)
                ->where('semester_id', $activeSem?->id)
                ->first();

            if ($wk) {
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

                Forms\Components\Select::make('grade')
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                        'D' => 'D',
                    ])
                    ->required(),

                Forms\Components\Textarea::make('deskripsi')
                     ->label('Deskripsi')
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
                Tables\Columns\TextColumn::make('grade')
                    ->sortable(),
                Tables\Columns\TextColumn::make('deskripsi')
                    ->limit(50),
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
            'index' => Pages\ListNilaiSosials::route('/'),
            'create' => Pages\CreateNilaiSosial::route('/create'),
            'edit' => Pages\EditNilaiSosial::route('/{record}/edit'),
        ];
    }
}
