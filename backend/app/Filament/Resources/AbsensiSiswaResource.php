<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AbsensiSiswaResource\Pages;
use App\Filament\Resources\AbsensiSiswaResource\RelationManagers;
use App\Models\AbsensiSiswa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AbsensiSiswaResource extends BaseResource
{
    protected static ?string $model = AbsensiSiswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    public static function getNavigationGroup(): ?string
    {
        return auth()->user()->hasRole('ptk') ? 'Akademik Guru' : 'Akademik';
    }
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Absensi Mapel';

    public static function shouldRegisterNavigation(): bool
    {
        // Hide for Wali Kelas to avoid confusion (they use Rekap Absensi)
        // If user is BOTH, they might still need this for their subjects?
        // User request: "pada Role Wali Kelas, Menu Absensi Siswa ini berbeda... Absensi ini untuk seluruh hari"
        // Implies when ACTING as Wali Kelas, they want the Rekap.
        // If they teach subjects, they might need "Absensi Mapel".
        // Let's show it if they are PTK, but label distinctively.
        // Wait, user said "Diffrent with Absensi Siswa pada Role PTK".
        // So PTK -> Absensi Siswa (Daily). Wali Kelas -> Absensi Siswa (Rekap).
        // I renamed this to "Absensi Mapel" to distinguish.
        // Let's keep it visible for PTK.
        // But if pure user wants "Wali Kelas" experience, maybe hide?
        // I will hide if user has role wali_kelas to be safe and strictly follow "Wali Kelas role behavior".
        // Or better: Show both if both roles, but distinct labels.
        // Current setup: This is "Absensi Mapel". Rekap is "Absensi Siswa".
        
        if (auth()->user()->hasRole('super_admin')) {
            return false;
        }
        return auth()->user()->hasRole('ptk');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                         // Schedule Selector for Teachers
                        Forms\Components\Select::make('schedule_selector')
                            ->label('Jadwal Mata Pelajaran')
                            ->options(function () {
                                $user = auth()->user();
                                if (!$user->hasRole('ptk') || !$user->guru) return [];

                                $options = [];
                                $schedules = \App\Models\JadwalGuru::with(['mataPelajaran', 'rombel'])
                                    ->where('guru_id', $user->guru->id)
                                    ->get();

                                foreach ($schedules as $jadwal) {
                                    if (!$jadwal->mataPelajaran || !$jadwal->rombel) continue;
                                    // Key: mapelId_rombelId
                                    $key = "{$jadwal->mata_pelajaran_id}_{$jadwal->rombel_id}";
                                    $label = "{$jadwal->mataPelajaran->nama} - {$jadwal->rombel->nama}";
                                    $options[$key] = $label;
                                }
                                return array_unique($options);
                            })
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (!$state) return;
                                list($mapelId, $rombelId) = explode('_', $state);
                                $set('mata_pelajaran_id', $mapelId);
                                $set('rombel_id', $rombelId);
                                $set('guru_id', auth()->user()->guru->id);
                            })
                            ->visible(fn () => auth()->user()->hasRole('ptk'))
                            ->dehydrated(false)
                            ->required(),

                        Forms\Components\Select::make('rombel_id')
                            ->relationship('rombel', 'nama')
                            ->label('Rombel')
                            ->live()
                            ->required()
                            ->visible(fn () => !auth()->user()->hasRole('ptk')), // Visible only for Non-PTK

                        Forms\Components\Hidden::make('rombel_id')
                            ->visible(fn () => auth()->user()->hasRole('ptk')), // Active only for PTK

                        Forms\Components\Select::make('mata_pelajaran_id')
                            ->relationship('mataPelajaran', 'nama')
                            ->label('Mata Pelajaran')
                            ->required()
                            ->visible(fn () => !auth()->user()->hasRole('ptk')), // Visible only for Non-PTK

                        Forms\Components\Hidden::make('mata_pelajaran_id')
                             ->visible(fn () => auth()->user()->hasRole('ptk')), // Active only for PTK
                            
                        Forms\Components\Hidden::make('guru_id')
                            ->default(fn() => auth()->user()->hasRole('ptk') ? auth()->user()->guru?->id : null),

                        Forms\Components\Select::make('siswa_id')
                            ->label('Siswa')
                            ->options(function (Forms\Get $get) {
                                $rombelId = $get('rombel_id');
                                if (!$rombelId) return [];
                                return \App\Models\Siswa::whereHas('rombels', function ($q) use ($rombelId) {
                                    $q->where('rombel_id', $rombelId);
                                })->pluck('nama_lengkap', 'id');
                            })
                            ->preload()
                            ->searchable()
                            ->required(),
                    ])->columnSpanFull(),

                Forms\Components\DatePicker::make('tanggal')
                    ->default(now())
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        'hadir' => 'Hadir',
                        'izin' => 'Izin',
                        'sakit' => 'Sakit',
                        'alpa' => 'Alpha',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('keterangan')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();
                if ($user->hasRole('ptk') && $user->guru) {
                    $query->where('guru_id', $user->guru->id);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('mataPelajaran.nama')
                    ->label('Mapel')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('rombel.nama')
                    ->label('Kelas')
                    ->sortable(),
                Tables\Columns\TextColumn::make('siswa.nama_lengkap')
                    ->label('Siswa')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'hadir' => 'success',
                        'izin' => 'warning',
                        'sakit' => 'info',
                        'alpa' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('guru.nama_lengkap')
                    ->label('Guru')
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
            'index' => Pages\ListAbsensiSiswas::route('/'),
            'create' => Pages\CreateAbsensiSiswa::route('/create'),
            'edit' => Pages\EditAbsensiSiswa::route('/{record}/edit'),
        ];
    }
}
