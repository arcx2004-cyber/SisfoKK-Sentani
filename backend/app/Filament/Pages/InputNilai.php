<?php

namespace App\Filament\Pages;

use App\Models\MataPelajaran;
use App\Models\Rombel;
use App\Models\Semester;
use App\Models\TujuanPembelajaran;
use App\Models\NilaiTujuanPembelajaran;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;

class InputNilai extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-pencil-square';
    protected static ?string $navigationGroup = 'Akademik Guru';
    protected static ?string $navigationLabel = 'Input Nilai';
    protected static ?string $title = 'Input Nilai';
    protected static string $view = 'filament.pages.input-nilai';

    public static function shouldRegisterNavigation(): bool
    {
        if (auth()->user()->hasRole('super_admin')) {
            return false;
        }
        return auth()->user()->hasRole('ptk');
    }

    public ?string $rombel_id = null;
    public ?string $mata_pelajaran_id = null;
    public ?string $semester_id = null;

    public $students = [];
    public $tps = [];
    public $scores = []; // [siswa_id => [tp_id => score]]

    public function mount()
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('semester_id')
                    ->label('Semester')
                    ->options(Semester::where('is_active', true)->get()->mapWithKeys(fn ($s) => [$s->id => $s->full_name]))
                    ->default(Semester::where('is_active', true)->first()?->id)
                    ->required()
                    ->live(),
                Select::make('rombel_id')
                    ->label('Rombel / Kelas')
                    ->options(function (Get $get) {
                        $user = auth()->user();
                        if (!$user->guru) return [];
                        
                        // Get only Rombels where the teacher has a schedule in the selected semester
                        return \App\Models\JadwalGuru::where('guru_id', $user->guru->id)
                            ->when($get('semester_id'), fn($q) => $q->where('semester_id', $get('semester_id')))
                            ->with('rombel')
                            ->get()
                            ->pluck('rombel.nama', 'rombel_id')
                            ->unique();
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadData()),
                Select::make('mata_pelajaran_id')
                    ->label('Mata Pelajaran')
                    ->options(function (Get $get) {
                        $user = auth()->user();
                        if (!$user->guru) return [];

                        // Filter mapels by schedule for this teacher and possibly selected rombel
                        return \App\Models\JadwalGuru::where('guru_id', $user->guru->id)
                            ->when($get('semester_id'), fn($q) => $q->where('semester_id', $get('semester_id')))
                            ->when($get('rombel_id'), fn($q) => $q->where('rombel_id', $get('rombel_id')))
                            ->with('mataPelajaran')
                            ->get()
                            ->pluck('mataPelajaran.nama', 'mata_pelajaran_id')
                            ->unique();
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn () => $this->loadData()),
            ])->columns(3);
    }

    public function loadData()
    {
        $this->students = [];
        $this->tps = [];
        $this->scores = [];

        if (!$this->rombel_id || !$this->mata_pelajaran_id || !$this->semester_id) {
            return;
        }

        // 1. Get Rombel first to know the Tingkat (Class Level)
        $rombel = Rombel::with('siswas')->find($this->rombel_id);
        if (!$rombel) return;

        $tingkat = $rombel->tingkat;

        // 2. Get TPs for this Mapel + Semester + Grade Level
        $mapelId = $this->mata_pelajaran_id;
        $semesterId = $this->semester_id;
        
        $this->tps = TujuanPembelajaran::whereHas('capaianPembelajaran', function ($q) use ($mapelId, $semesterId, $tingkat) {
            $q->where('mata_pelajaran_id', $mapelId)
              ->where('semester_id', $semesterId)
              ->where('kelas', $tingkat);
        })->get();

        // 3. Get Students (using already fetched rombel)
        $this->students = $rombel->siswas;

        // Initialize all scores to avoid Livewire binding issues
        foreach ($this->students as $student) {
            foreach ($this->tps as $tp) {
                $this->scores[$student->id][$tp->id] = null;
            }
        }

        // 3. Load existing scores
        $existingScores = NilaiTujuanPembelajaran::whereIn('siswa_id', $this->students->pluck('id'))
            ->whereIn('tujuan_pembelajaran_id', $this->tps->pluck('id'))
            ->get();

        foreach ($existingScores as $score) {
            $this->scores[$score->siswa_id][$score->tujuan_pembelajaran_id] = $score->nilai;
        }
    }

    public function save()
    {
        try {
            foreach ($this->scores as $siswaId => $tpScores) {
                foreach ($tpScores as $tpId => $nilai) {
                    if ($nilai !== null && $nilai !== '') {
                        NilaiTujuanPembelajaran::updateOrCreate(
                            ['siswa_id' => $siswaId, 'tujuan_pembelajaran_id' => $tpId],
                            ['nilai' => $nilai]
                        );
                    }
                }
            }

            Notification::make()
                ->title('Nilai berhasil disimpan')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menyimpan nilai')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Nilai')
                ->submit('save'),
        ];
    }
}
