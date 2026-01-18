<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PembayaranSpp;
use App\Models\PembayaranKegiatan;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintController extends Controller
{
    public function printLaporanSpp(Request $request)
    {
        $unitId = $request->input('unit_id');
        $tahun = $request->input('tahun');
        $status = $request->input('status');

        $query = PembayaranSpp::with(['siswa.unit', 'siswa.rombels']);

        if ($unitId) {
            $query->whereHas('siswa', fn($q) => $q->where('unit_id', $unitId));
        }
        if ($tahun) {
            $query->where('tahun', $tahun);
        }
        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        $data = $query->orderBy('tahun', 'desc')->orderBy('bulan', 'asc')->get();
        $title = 'Laporan Pembayaran SPP ' . ($status ? ucfirst(str_replace('_', ' ', $status)) : 'Semua Status');

        $pdf = Pdf::loadView('print.laporan-spp', compact('data', 'title', 'tahun', 'status'));
        return $pdf->stream('laporan-spp.pdf');
    }

    public function printLaporanKegiatan(Request $request)
    {
        $unitId = $request->input('unit_id');
        $status = $request->input('status');

        $query = PembayaranKegiatan::with(['siswa', 'siswa.unit', 'siswa.rombels', 'tarifKegiatan']);

        if ($unitId) {
            $query->whereHas('siswa', fn($q) => $q->where('unit_id', $unitId));
        }
        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        $data = $query->orderBy('created_at', 'desc')->get();
        $title = 'Laporan Pembayaran Kegiatan ' . ($status ? ucfirst(str_replace('_', ' ', $status)) : 'Semua Status');

        $pdf = Pdf::loadView('print.laporan-kegiatan', compact('data', 'title', 'status'));
        return $pdf->stream('laporan-kegiatan.pdf');
    }
}
