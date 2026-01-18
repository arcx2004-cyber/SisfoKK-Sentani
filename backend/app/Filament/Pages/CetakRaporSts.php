<?php

namespace App\Filament\Pages;

use App\Models\Rombel;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\Semester;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Barryvdh\DomPDF\Facade\Pdf;

class CetakRaporSts extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-printer';
    
    protected static ?string $navigationLabel = 'Cetak Rapor';
    
    protected static ?string $title = 'Cetak Rapor';
    
    protected static ?string $navigationGroup = 'Akademik'; // Or 'Laporan' if available

    protected static string $view = 'filament.pages.cetak-rapor-sts';

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['admin', 'administrator', 'wali_kelas', 'tendik']);
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user->hasAnyRole(['super_admin', 'admin', 'administrator', 'wali_kelas', 'tendik']);
    }

    public ?array $data = [];

    public function mount(): void
    {
        $activeTa = TahunAjaran::where('is_active', true)->first();
        $activeSem = Semester::where('is_active', true)->first();

        $data = [
            'tahun_ajaran_id' => $activeTa?->id,
            'semester_id' => $activeSem?->id,
            'rombel_id' => null,
            'siswa_id' => null,
        ];

        // Auto-select Rombel for Wali Kelas
        $user = auth()->user();
        if ($user->hasRole('wali_kelas') && $user->guru && $activeSem) {
            $wk = \App\Models\WaliKelas::where('guru_id', $user->guru->id)
                ->where('semester_id', $activeSem->id)
                ->first();
            if ($wk) {
                $data['rombel_id'] = $wk->rombel_id;
            }
        }

        $this->form->fill($data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('jenis_rapor')
                    ->label('Jenis Rapor')
                    ->options([
                        'sts' => 'Sumatif Tengah Semester (STS)',
                        'sas' => 'Sumatif Akhir Semester (SAS/PAT)',
                    ])
                    ->default('sts')
                    ->required(),
                    
                Select::make('tahun_ajaran_id')
                    ->label('Tahun Ajaran')
                    ->options(TahunAjaran::all()->pluck('nama', 'id'))
                    ->required()
                    ->live(),
                
                Select::make('semester_id')
                    ->label('Semester')
                    ->options(Semester::where('is_active', true)->with('tahunAjaran')->get()->pluck('full_name', 'id'))
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('siswa_id', null);
                        
                        // Auto-update Rombel for Wali Kelas when Semester changes
                        $user = Auth::user();
                        if ($user->hasRole('wali_kelas') && $user->guru) {
                            $wk = \App\Models\WaliKelas::where('guru_id', $user->guru->id)
                                ->where('semester_id', $state)
                                ->first();
                            if ($wk) {
                                $set('rombel_id', $wk->rombel_id);
                            } else {
                                $set('rombel_id', null);
                            }
                        }
                    }),

                Select::make('rombel_id')
                    ->label('Rombel / Kelas')
                    ->hidden(fn () => Auth::user()->hasRole('wali_kelas'))
                    ->dehydrated()
                    ->options(function (Get $get) {
                        $user = Auth::user();
                        $taId = $get('tahun_ajaran_id'); // Keep context if needed, though mostly relying on WaliKelas now
                        $semId = $get('semester_id');
                        
                        if (!$taId) return [];

                        $query = Rombel::where('tahun_ajaran_id', $taId);
                        
                        // Fallback filter if not hidden (e.g. Admin view) or if permission check differs
                        if ($user->hasRole('wali_kelas')) {
                             $guruId = $user->guru?->id;
                             if ($guruId && $semId) {
                                  // Filter likely single rombel
                                  $rombelIs = \App\Models\WaliKelas::where('guru_id', $guruId)
                                        ->where('semester_id', $semId)
                                        ->pluck('rombel_id');
                                  if ($rombelIs->isNotEmpty()) {
                                      $query->whereIn('id', $rombelIs);
                                  }
                             }
                        }

                        return $query->pluck('nama', 'id');
                    })
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('siswa_id', null)),

                Select::make('siswa_id')
                    ->label('Siswa')
                    ->options(function (Get $get) {
                        $rombelId = $get('rombel_id');
                        if (!$rombelId) return [];
                        
                        return Siswa::whereHas('rombels', function ($q) use ($rombelId) {
                            $q->where('rombels.id', $rombelId);
                        })->pluck('nama_lengkap', 'id');
                    })
                    ->required()
                    ->searchable(),
            ])
            ->statePath('data');
    }

    public function printAction()
    {
        $data = $this->form->getState();
        $siswaId = $data['siswa_id'];
        $jenisRapor = $data['jenis_rapor'];
        
        // Handle hidden rombel_id for Wali Kelas
        $rombelId = $data['rombel_id'] ?? null;
        if (!$rombelId && Auth::user()->hasRole('wali_kelas') && Auth::user()->guru) {
            $semId = $data['semester_id'];
            $wk = \App\Models\WaliKelas::where('guru_id', Auth::user()->guru->id)
                ->where('semester_id', $semId)
                ->first();
            $rombelId = $wk?->rombel_id;
        }

        $taId = $data['tahun_ajaran_id'];
        $semId = $data['semester_id'];

        $routeName = match ($jenisRapor) {
            'sas' => 'print.rapor-sas',
            default => 'print.rapor-sts',
        };

        $url = route($routeName, [
            'siswa_id' => $siswaId,
            'rombel_id' => $rombelId,
            'tahun_ajaran_id' => $taId,
            'semester_id' => $semId
        ]);

        // Open in new tab using JavaScript
        $this->js("window.open('{$url}', '_blank')");
    }
}
