<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms;
use Filament\Notifications\Notification;
use App\Models\Siswa;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\PenilaianSikap;
use App\Models\WaliKelas;

class InputPenilaianSikap extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';

    protected static ?string $navigationGroup = 'Administrasi Kelas';

    protected static ?string $navigationLabel = 'Penilaian Sikap';

    protected static ?string $title = 'Input Penilaian Sikap';

    protected static string $view = 'filament.pages.input-penilaian-sikap';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        if ($user->hasRole('super_admin')) {
            return false;
        }
        return $user->hasAnyRole(['wali_kelas', 'admin', 'administrator']);
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'admin', 'administrator', 'wali_kelas']);
    }

    protected function getWaliKelasData(): ?array
    {
        $user = auth()->user();
        
        if ($user->hasRole('wali_kelas') && $user->guru) {
            $activeSemester = Semester::where('is_active', true)->first();
            $waliKelas = WaliKelas::where('guru_id', $user->guru->id)
                ->where('semester_id', $activeSemester?->id)
                ->first();
            
            if ($waliKelas) {
                return [
                    'rombel_id' => $waliKelas->rombel_id,
                    'semester_id' => $activeSemester->id,
                    'tahun_ajaran_id' => TahunAjaran::where('is_active', true)->first()?->id,
                ];
            }
        }
        
        return null;
    }

    public function mount(): void
    {
        $students = $this->getStudents();
        $wkData = $this->getWaliKelasData();
        
        $nilai = [];
        foreach ($students as $siswa) {
            $existing = PenilaianSikap::where('siswa_id', $siswa->id)
                ->where('semester_id', $wkData['semester_id'] ?? null)
                ->first();
            
            $nilai[] = [
                'siswa_id' => $siswa->id,
                'nama_siswa' => $siswa->nama_lengkap,
                'kedisiplinan' => $existing?->kedisiplinan ?? '',
                'kejujuran' => $existing?->kejujuran ?? '',
                'kesopanan' => $existing?->kesopanan ?? '',
                'kebersihan' => $existing?->kebersihan ?? '',
                'kepedulian' => $existing?->kepedulian ?? '',
                'tanggung_jawab' => $existing?->tanggung_jawab ?? '',
                'percaya_diri' => $existing?->percaya_diri ?? '',
            ];
        }
        
        $this->form->fill(['nilai' => $nilai]);
    }

    protected function getStudents()
    {
        $user = auth()->user();
        
        if ($user->hasRole('wali_kelas') && $user->guru) {
            $activeSemester = Semester::where('is_active', true)->first();
            $waliKelas = WaliKelas::where('guru_id', $user->guru->id)
                ->where('semester_id', $activeSemester?->id)
                ->first();
            
            if ($waliKelas) {
                return Siswa::whereHas('rombels', function ($q) use ($waliKelas) {
                    $q->where('rombels.id', $waliKelas->rombel_id);
                })->orderBy('nama_lengkap')->get();
            }
        }
        
        return collect();
    }

    protected static function getSikapOptions(): array
    {
        return [
            'A' => 'A',
            'B' => 'B',
            'C' => 'C',
            'D' => 'D',
        ];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Input Penilaian Sikap')
                    ->description('Masukkan nilai sikap untuk semua siswa di rombel Anda')
                    ->schema([
                        Forms\Components\Repeater::make('nilai')
                            ->schema([
                                Forms\Components\Hidden::make('siswa_id'),
                                Forms\Components\TextInput::make('nama_siswa')
                                    ->label('Nama Siswa')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpan(2),
                                Forms\Components\Select::make('kedisiplinan')
                                    ->label('Disiplin')
                                    ->options(self::getSikapOptions())
                                    ->columnSpan(1),
                                Forms\Components\Select::make('kejujuran')
                                    ->label('Jujur')
                                    ->options(self::getSikapOptions())
                                    ->columnSpan(1),
                                Forms\Components\Select::make('kesopanan')
                                    ->label('Sopan')
                                    ->options(self::getSikapOptions())
                                    ->columnSpan(1),
                                Forms\Components\Select::make('kebersihan')
                                    ->label('Bersih')
                                    ->options(self::getSikapOptions())
                                    ->columnSpan(1),
                                Forms\Components\Select::make('kepedulian')
                                    ->label('Peduli')
                                    ->options(self::getSikapOptions())
                                    ->columnSpan(1),
                                Forms\Components\Select::make('tanggung_jawab')
                                    ->label('Tgg Jwb')
                                    ->options(self::getSikapOptions())
                                    ->columnSpan(1),
                                Forms\Components\Select::make('percaya_diri')
                                    ->label('PD')
                                    ->options(self::getSikapOptions())
                                    ->columnSpan(1),
                            ])
                            ->columns(10)
                            ->addable(false)
                            ->deletable(false)
                            ->reorderable(false)
                            ->itemLabel(fn (array $state): ?string => $state['nama_siswa'] ?? null),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $wkData = $this->getWaliKelasData();
        
        if (!$wkData) {
            Notification::make()
                ->title('Error: Data Wali Kelas tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        $saved = 0;
        foreach ($data['nilai'] as $item) {
            // Check if at least one field is filled
            $hasSikap = !empty($item['kedisiplinan']) || !empty($item['kejujuran']) || 
                        !empty($item['kesopanan']) || !empty($item['kebersihan']) || 
                        !empty($item['kepedulian']) || !empty($item['tanggung_jawab']) || 
                        !empty($item['percaya_diri']);
            
            if ($hasSikap) {
                PenilaianSikap::updateOrCreate(
                    [
                        'siswa_id' => $item['siswa_id'],
                        'rombel_id' => $wkData['rombel_id'],
                        'semester_id' => $wkData['semester_id'],
                    ],
                    [
                        'tahun_ajaran_id' => $wkData['tahun_ajaran_id'],
                        'kedisiplinan' => $item['kedisiplinan'] ?? null,
                        'kejujuran' => $item['kejujuran'] ?? null,
                        'kesopanan' => $item['kesopanan'] ?? null,
                        'kebersihan' => $item['kebersihan'] ?? null,
                        'kepedulian' => $item['kepedulian'] ?? null,
                        'tanggung_jawab' => $item['tanggung_jawab'] ?? null,
                        'percaya_diri' => $item['percaya_diri'] ?? null,
                    ]
                );
                $saved++;
            }
        }

        Notification::make()
            ->title("Berhasil menyimpan {$saved} penilaian sikap")
            ->success()
            ->send();
    }
}
