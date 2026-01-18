<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CapaianPembelajaranResource\Pages;
use App\Models\CapaianPembelajaran;
use App\Models\MataPelajaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CapaianPembelajaranResource extends BaseResource
{
    protected static ?string $model = CapaianPembelajaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    public static function getNavigationGroup(): ?string
    {
        return auth()->user()->hasRole('ptk') ? 'Akademik Guru' : 'Akademik';
    }
    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole('super_admin')) {
             return false;
        }
        return parent::shouldRegisterNavigation();
    }
    protected static ?int $navigationSort = 1;
    public static function getNavigationLabel(): string
    {
        return auth()->user()->hasAnyRole(['ptk', 'wali_kelas']) ? 'Daftar CP & TP' : 'Tujuan & Capaian Pembelajaran';
    }
    protected static ?string $modelLabel = 'Tujuan & Capaian Pembelajaran';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Mata Pelajaran')
                    ->schema([
                        // Virtual field for PTK to select Mapel + Kelas combined
                        Forms\Components\Select::make('mapel_kelas_selector')
                            ->label('Mata Pelajaran')
                            ->options(function () {
                                $user = Auth::user();
                                if (!$user->hasRole('ptk') || !$user->guru) return [];

                                $options = [];
                                // Get schedules to find distinct mapel + tingkat combinations
                                $schedules = \App\Models\JadwalGuru::with(['mataPelajaran', 'rombel'])
                                    ->where('guru_id', $user->guru->id)
                                    ->get();

                                foreach ($schedules as $jadwal) {
                                    if (!$jadwal->mataPelajaran || !$jadwal->rombel) continue;
                                    
                                    $mapelId = $jadwal->mata_pelajaran_id;
                                    $tingkat = $jadwal->rombel->tingkat;
                                    $mapelName = $jadwal->mataPelajaran->nama;
                                    
                                    // Key format: mapelId_tingkat_unitId
                                    $key = "{$mapelId}_{$tingkat}_{$jadwal->rombel->unit_id}";
                                    $label = "{$mapelName} - Kelas {$tingkat}";
                                    
                                    $options[$key] = $label;
                                }
                                
                                // Unique options
                                return array_unique($options);
                            })
                            ->searchable()
                            ->required()
                            ->visible(fn () => Auth::user()->hasRole('ptk'))
                            ->dehydrated(false)
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                if (!$state) return;
                                list($mapelId, $tingkat, $unitId) = explode('_', $state);
                                
                                $set('mata_pelajaran_id', $mapelId);
                                $set('kelas', $tingkat);
                                $set('unit_id', $unitId); // Set Unit based on selection
                                
                                // Auto-set Fase based on Tingkat
                                $fase = match(true) {
                                    $tingkat <= 2 => 'A',
                                    $tingkat <= 4 => 'B',
                                    $tingkat <= 6 => 'C',
                                    $tingkat <= 9 => 'D',
                                    $tingkat == 10 => 'E',
                                    default => 'F',
                                };
                                $set('fase', $fase);
                            }),

                        // Regular fields (Hidden for PTK if auto-filled)
                        Forms\Components\Select::make('unit_id')
                            ->label('Unit')
                            ->relationship('unit', 'nama')
                            ->required()
                            ->reactive()
                            ->visible(fn () => !Auth::user()->hasRole('ptk')), // Visible only for Non-PTK

                        Forms\Components\Hidden::make('unit_id')
                            ->visible(fn () => Auth::user()->hasRole('ptk')),

                        Forms\Components\Select::make('mata_pelajaran_id')
                            ->label('Mata Pelajaran')
                            ->relationship('mataPelajaran', 'nama', fn ($query, $get) => 
                                $get('unit_id') ? $query->where('unit_id', $get('unit_id')) : $query
                            )
                            ->searchable()
                            ->preload()
                            ->required()
                            ->visible(fn () => !Auth::user()->hasRole('ptk')) // Visible only for Non-PTK
                            ->options(function ($get) {
                                $unitId = $get('unit_id');
                                if ($unitId) {
                                    return MataPelajaran::where('unit_id', $unitId)->pluck('nama', 'id');
                                }
                                return MataPelajaran::all()->pluck('nama', 'id');
                            }),

                        Forms\Components\Hidden::make('mata_pelajaran_id')
                            ->visible(fn () => Auth::user()->hasRole('ptk')),

                        Forms\Components\Select::make('fase')
                            ->label('Fase')
                            ->options([
                                'A' => 'Fase A (Kelas 1-2 SD)',
                                'B' => 'Fase B (Kelas 3-4 SD)',
                                'C' => 'Fase C (Kelas 5-6 SD)',
                                'D' => 'Fase D (Kelas 7-9 SMP)',
                                'E' => 'Fase E (Kelas 10 SMA)',
                                'F' => 'Fase F (Kelas 11-12 SMA)',
                            ])
                            ->required()
                            ->visible(fn () => !Auth::user()->hasRole('ptk')), // Visible only for Non-PTK

                        Forms\Components\Hidden::make('fase')
                            ->visible(fn () => Auth::user()->hasRole('ptk')),

                        Forms\Components\Select::make('kelas')
                            ->label('Kelas')
                            ->options([
                                '1' => 'Kelas 1', '2' => 'Kelas 2', '3' => 'Kelas 3',
                                '4' => 'Kelas 4', '5' => 'Kelas 5', '6' => 'Kelas 6',
                                '7' => 'Kelas 7', '8' => 'Kelas 8', '9' => 'Kelas 9',
                                '10' => 'Kelas 10', '11' => 'Kelas 11', '12' => 'Kelas 12',
                            ])
                            ->required()
                            ->visible(fn () => !Auth::user()->hasRole('ptk')), // Visible only for Non-PTK

                        Forms\Components\Hidden::make('kelas')
                            ->visible(fn () => Auth::user()->hasRole('ptk')),

                        Forms\Components\Select::make('semester_id')
                            ->relationship('semester', 'tipe')
                            ->label('Semester')
                            ->required()
                            ->default(fn() => \App\Models\Semester::where('is_active', true)->first()?->id)
                            ->visible(fn () => !Auth::user()->hasRole('ptk')), // Visible only for Non-PTK

                        Forms\Components\Hidden::make('semester_id')
                            ->default(fn() => \App\Models\Semester::where('is_active', true)->first()?->id)
                            ->visible(fn () => Auth::user()->hasRole('ptk')),
                    ])->columns(2),
                
                Forms\Components\Section::make('Capaian Pembelajaran (CP)')
                    ->schema([
                         Forms\Components\TextInput::make('kode')
                            ->label('Kode CP')
                            ->placeholder('Contoh: CP-MAT-7.1')
                            ->required(),
                         Forms\Components\Textarea::make('deskripsi')
                            ->label('Isi Capaian Pembelajaran')
                            ->rows(3)
                            ->required(),
                    ]),

                Forms\Components\Section::make('Tujuan Pembelajaran (TP)')
                    ->schema([
                        Forms\Components\Repeater::make('tujuanPembelajarans')
                            ->relationship()
                            ->schema([
                                Forms\Components\TextInput::make('kode')
                                    ->label('Kode TP')
                                    ->placeholder('TP-7.1.1')
                                    ->required(),
                                Forms\Components\Textarea::make('deskripsi')
                                    ->label('Isi Tujuan Pembelajaran')
                                    ->rows(2)
                                    ->required(),
                            ])
                            ->itemLabel('Tujuan Pembelajaran')
                            ->addActionLabel('Tambah TP')
                            ->collapsible()
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $user = Auth::user();
                
                // Filter for PTK role
                if ($user->hasRole('ptk') && !$user->hasAnyRole(['super_admin', 'admin', 'administrator', 'kepsek']) && $user->guru) {
                    // Get mapel IDs from JadwalGuru
                    $mapelIds = \App\Models\JadwalGuru::where('guru_id', $user->guru->id)
                        ->pluck('mata_pelajaran_id')
                        ->unique();
                    
                    if ($mapelIds->isNotEmpty()) {
                        $query->whereIn('mata_pelajaran_id', $mapelIds);
                    } else {
                        $query->whereNull('id'); 
                    }
                }
                
                // Filter for Wali Kelas - only show CPs for subjects scheduled in their rombel
                if ($user->hasRole('wali_kelas') && !$user->hasAnyRole(['super_admin', 'admin', 'administrator']) && $user->guru) {
                    $activeSemester = \App\Models\Semester::getActive();
                    
                    if ($activeSemester) {
                        // Get wali_kelas assignment
                        $waliKelas = \App\Models\WaliKelas::where('guru_id', $user->guru->id)
                            ->where('semester_id', $activeSemester->id)
                            ->first();
                        
                        if ($waliKelas) {
                            // Get mata pelajaran IDs that are scheduled in this rombel
                            $mapelIds = \App\Models\JadwalGuru::where('rombel_id', $waliKelas->rombel_id)
                                ->where('semester_id', $activeSemester->id)
                                ->pluck('mata_pelajaran_id')
                                ->unique();
                            
                            if ($mapelIds->isNotEmpty()) {
                                $query->whereIn('mata_pelajaran_id', $mapelIds);
                            } else {
                                // No schedule found, show nothing
                                $query->whereNull('id');
                            }
                        } else {
                            // No assignment, show nothing
                            $query->whereNull('id');
                        }
                    }
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('mataPelajaran.nama')
                    ->label('Mata Pelajaran')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('semester.tipe')
                    ->label('Semester')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas')
                    ->label('Kelas/Fase')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode CP')
                    ->searchable(),
                 Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Isi CP')
                    ->limit(50),
                Tables\Columns\TextColumn::make('tujuan_pembelajarans_count')
                    ->counts('tujuanPembelajarans')
                    ->label('Jml TP'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mata_pelajaran_id')
                    ->relationship('mataPelajaran', 'nama')
                    ->label('Mata Pelajaran'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('generate_rpp')
                    ->label('Generate RPP (AI)')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->visible(fn () => !auth()->user()->hasRole('wali_kelas')) // Hide for Wali Kelas
                    ->requiresConfirmation()
                    ->modalHeading('Generate RPP Otomatis')
                    ->modalDescription('Sistem akan membuat RPP berdasarkan CP dan TP ini menggunakan AI (Gemini Pro). Proses ini mungkin memakan waktu beberapa detik.')
                    ->action(function (CapaianPembelajaran $record) {
                        try {
                            $tps = $record->tujuanPembelajarans->pluck('deskripsi')->implode("\n- ");
                            $prompt = "Sebagai asisten guru profesional, buatkan Rencana Pelaksanaan Pembelajaran (RPP) yang lengkap dan terstruktur untuk:\n\n" .
                                      "Mata Pelajaran: " . $record->mataPelajaran->nama . "\n" .
                                      "Kelas/Fase: Kelas " . $record->kelas . "\n" .
                                      "Capaian Pembelajaran: " . $record->deskripsi . "\n" .
                                      "Tujuan Pembelajaran:\n- " . $tps . "\n\n" .
                                      "Format RPP harus dalam Markdown, mencakup:\n" .
                                      "1. Identitas Mata Pelajaran (Termasuk Fase/Kelas)\n" .
                                      "2. Profil Pelajar Pancasila\n" .
                                      "3. Materi Pembelajaran\n" .
                                      "4. Metode Pembelajaran\n" .
                                      "5. Kegiatan Pembelajaran (Pendahuluan, Inti, Penutup)\n" .
                                      "6. Asesmen/Penilaian\n\n" .
                                      "Berikan output hanya konten RPP dalam format Markdown.";

                            $result = \Gemini\Laravel\Facades\Gemini::generativeModel('gemini-flash-latest')->generateContent($prompt);
                            $content = $result->text();

                            if ($content) {
                                $rpp = \App\Models\Rpp::create([
                                    'capaian_pembelajaran_id' => $record->id,
                                    'guru_id' => Auth::user()->guru ? Auth::user()->guru->id : 1, // Fallback to ID 1 if not a guru user
                                    'konten_rpp' => $content,
                                    'status' => 'generated',
                                ]);
                                
                                \Filament\Notifications\Notification::make()
                                    ->title('RPP Berhasil Digenerate')
                                    ->success()
                                    ->actions([
                                        \Filament\Notifications\Actions\Action::make('view')
                                            ->label('Periksa RPP')
                                            ->url(\App\Filament\Resources\RppResource::getUrl('edit', ['record' => $rpp])),
                                    ])
                                    ->send();
                            }
                        } catch (\Exception $e) {
                             \Illuminate\Support\Facades\Log::error('Gemini RPP Error: ' . $e->getMessage());
                             \Filament\Notifications\Notification::make()
                                    ->title('Gagal Generate RPP')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                        }
                    }),
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
            'index' => Pages\ListCapaianPembelajarans::route('/'),
            'create' => Pages\CreateCapaianPembelajaran::route('/create'),
            'edit' => Pages\EditCapaianPembelajaran::route('/{record}/edit'),
        ];
    }
}
