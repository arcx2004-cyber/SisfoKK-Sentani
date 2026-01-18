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
use App\Models\NilaiSpiritual;
use App\Models\WaliKelas;

class InputPenilaianSpiritual extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationGroup = 'Administrasi Kelas';

    protected static ?string $navigationLabel = 'Penilaian Spiritual';

    protected static ?string $title = 'Input Penilaian Spiritual';

    protected static string $view = 'filament.pages.input-penilaian-spiritual';

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

    public function mount(): void
    {
        $students = $this->getStudents();
        $activeSemester = Semester::where('is_active', true)->first();
        
        $nilai = [];
        foreach ($students as $siswa) {
            $existing = NilaiSpiritual::where('siswa_id', $siswa->id)
                ->where('semester_id', $activeSemester?->id)
                ->first();
            
            $nilai[] = [
                'siswa_id' => $siswa->id,
                'nama_siswa' => $siswa->nama_lengkap,
                'grade' => $existing?->grade ?? '',
                'deskripsi' => $existing?->deskripsi ?? '',
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Input Penilaian Spiritual')
                    ->description('Masukkan nilai spiritual untuk semua siswa di rombel Anda')
                    ->schema([
                        Forms\Components\Repeater::make('nilai')
                            ->schema([
                                Forms\Components\Hidden::make('siswa_id'),
                                Forms\Components\TextInput::make('nama_siswa')
                                    ->label('Nama Siswa')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->columnSpan(3),
                                Forms\Components\Select::make('grade')
                                    ->label('Grade')
                                    ->options([
                                        'A' => 'A',
                                        'B' => 'B',
                                        'C' => 'C',
                                        'D' => 'D',
                                    ])
                                    ->columnSpan(2),
                                Forms\Components\TextInput::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->columnSpan(4),
                            ])
                            ->columns(9)
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
        $activeSemester = Semester::where('is_active', true)->first();
        
        if (!$activeSemester) {
            Notification::make()
                ->title('Error: Semester aktif tidak ditemukan')
                ->danger()
                ->send();
            return;
        }

        $saved = 0;
        foreach ($data['nilai'] as $item) {
            if (!empty($item['grade'])) {
                NilaiSpiritual::updateOrCreate(
                    [
                        'siswa_id' => $item['siswa_id'],
                        'semester_id' => $activeSemester->id,
                    ],
                    [
                        'grade' => $item['grade'],
                        'deskripsi' => $item['deskripsi'] ?? null,
                    ]
                );
                $saved++;
            }
        }

        Notification::make()
            ->title("Berhasil menyimpan {$saved} nilai spiritual")
            ->success()
            ->send();
    }
}
