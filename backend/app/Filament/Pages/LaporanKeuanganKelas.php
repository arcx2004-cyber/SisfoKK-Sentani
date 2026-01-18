<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\WaliKelas;
use App\Models\TahunAjaran;
use App\Models\PembayaranSpp;
use App\Models\PembayaranKegiatan;
use App\Models\TarifKegiatan;

class LaporanKeuanganKelas extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Laporan Keuangan Siswa';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $title = 'Laporan Keuangan Kelas';
    protected static string $view = 'filament.pages.laporan-keuangan-kelas';

    public $rombel = null;
    public $students = [];

    public function mount()
    {
        $user = Auth::user();
        
        // Ensure user is related to a Guru
        if (!$user->guru) {
            return;
        }

        $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
        if (!$activeTahunAjaran) return;

        // Find Rombel assigned to this Guru as Wali Kelas for active/latest context
        // Try to find exact match for active semester/year if possible, 
        // otherwise default to most recent assignment
        $waliKelasEntry = WaliKelas::where('guru_id', $user->guru->id)
            ->whereHas('rombel', function($q) use ($activeTahunAjaran) {
                $q->where('tahun_ajaran_id', $activeTahunAjaran->id);
            })
            ->latest()
            ->first();

        if (!$waliKelasEntry) {
            // Fallback: try any active assignment
             $waliKelasEntry = WaliKelas::where('guru_id', $user->guru->id)->latest()->first();
        }

        if ($waliKelasEntry) {
            $this->rombel = $waliKelasEntry->rombel;
            $this->loadStudentData();
        }
    }

    protected function loadStudentData()
    {
        if (!$this->rombel) return;

        $rawStudents = $this->rombel->siswas()
            ->where('status', 'aktif')
            ->orderBy('nama_lengkap')
            ->get();

        $currentMonth = (int)date('m');
        $currentYear = (int)date('Y');
        
        // Determine academic year start/end months for calculation
        // Assuming simple July-June logic for "Tunggakan" calculation in active year
        $activeTahunAjaran = $this->rombel->tahunAjaran;
        // Logic check: Iterate months from start of AY until now to check unpaid SPP
        
        // Simply: Check specific SPP records generated
        
        foreach ($rawStudents as $siswa) {
            // 1. SPP Status Bulan Ini
            $sppBulanIni = PembayaranSpp::where('siswa_id', $siswa->id)
                ->where('bulan', $currentMonth)
                ->where('tahun', $currentYear)
                ->where('status', 'lunas')
                ->exists();

            // 2. Tunggakan SPP (Count unpaid 'pembayaran_spps' records that exist but aren't paid)
            // Only counts GENERATED bills. If bills aren't generated, it won't show as tunggakan.
            $tunggakanCount = PembayaranSpp::where('siswa_id', $siswa->id)
                ->where('status', '!=', 'lunas')
                ->where(function($q) use ($currentYear, $currentMonth) {
                    // Only count past or current bills, not future ones if generated
                    $q->where('tahun', '<', $currentYear)
                      ->orWhere(function($sub) use ($currentYear, $currentMonth) {
                          $sub->where('tahun', $currentYear)
                              ->where('bulan', '<=', $currentMonth);
                      });
                })
                ->count();

            // 3. Kegiatan Tahunan
            // Check all activities for this unit/year
            $tarifs = TarifKegiatan::where('unit_id', $siswa->unit_id)
                ->where('tahun_ajaran_id', $activeTahunAjaran->id)
                ->get();
            
            $statusKegiatan = 'Lunas';
            $paidCount = 0;
            $totalCount = $tarifs->count();

            if ($totalCount > 0) {
                foreach ($tarifs as $tarif) {
                    $payment = PembayaranKegiatan::where('siswa_id', $siswa->id)
                        ->where('tarif_kegiatan_id', $tarif->id)
                        ->first();
                    
                    if (!$payment || $payment->status != 'lunas') {
                        $statusKegiatan = 'Belum Lunas';
                    } else {
                        $paidCount++;
                    }
                }
                
                if ($statusKegiatan == 'Belum Lunas' && $paidCount > 0) {
                    $statusKegiatan = 'Sebagian';
                }
            } else {
                $statusKegiatan = '-'; // No activities
            }
            

            $this->students[] = [
                'nama' => $siswa->nama_lengkap,
                'nis' => $siswa->nis . ' / ' . $siswa->nisn,
                'spp_bulan_ini' => $sppBulanIni,
                'tunggakan_spp' => $tunggakanCount,
                'status_kegiatan' => $statusKegiatan,
            ];
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user()->hasRole('wali_kelas');
    }
}
