<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\PembayaranSpp;
use App\Models\PembayaranKegiatan;
use App\Models\TarifKegiatan;
use App\Models\TahunAjaran;
use App\Models\Semester;

class StatusKeuangan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationLabel = 'Status Keuangan';
    protected static ?int $navigationSort = 4;
    protected static ?string $title = 'Status Keuangan Saya';
    protected static string $view = 'filament.pages.status-keuangan';

    public $sppData = [];
    public $kegiatanData = [];

    public function mount()
    {
        $user = Auth::user();
        $student = $user->siswa;

        if (!$student) return;

        $activeTahunAjaran = TahunAjaran::where('is_active', true)->first();
        if (!$activeTahunAjaran) return;

        // 1. Load SPP Data for Active Year
        // Logic: Generate list of months for the academic year (July -> June)
        // Check payment for each.
        
        $months = [
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni'
        ];

        foreach ($months as $bulanNum => $bulanName) {
            $payment = PembayaranSpp::where('siswa_id', $student->id)
                ->where('bulan', $bulanNum)
                ->where('tahun', $bulanNum >= 7 ? substr($activeTahunAjaran->nama, 0, 4) : substr($activeTahunAjaran->nama, 5, 4)) // Approximation
                ->first();

            $this->sppData[] = [
                'bulan' => $bulanName,
                'status' => $payment && $payment->status == 'lunas' ? 'Lunas' : 'Belum Lunas',
                'tanggal' => $payment ? $payment->tanggal_bayar : '-',
                'nominal' => $payment ? 'Rp ' . number_format($payment->nominal_bayar, 0, ',', '.') : '-',
            ];
        }

        // 2. Load Kegiatan Data
        $tarifs = TarifKegiatan::where('unit_id', $student->unit_id)
            ->where('tahun_ajaran_id', $activeTahunAjaran->id)
            ->get();

        foreach ($tarifs as $tarif) {
            $payment = PembayaranKegiatan::where('siswa_id', $student->id)
                ->where('tarif_kegiatan_id', $tarif->id)
                ->first();

            $paid = $payment ? $payment->nominal_bayar : 0;
            $status = $payment && $payment->status == 'lunas' ? 'Lunas' : ($paid > 0 ? 'Sebagian' : 'Belum Lunas');

            $this->kegiatanData[] = [
                'kegiatan' => $tarif->nama_kegiatan,
                'tagihan' => 'Rp ' . number_format($tarif->nominal, 0, ',', '.'),
                'terbayar' => 'Rp ' . number_format($paid, 0, ',', '.'),
                'status' => $status,
                'tanggal' => $payment ? $payment->tanggal_bayar : '-',
            ];
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user() && Auth::user()->hasRole('siswa');
    }
}
