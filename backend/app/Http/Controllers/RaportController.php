<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Services\RaportStsService;
use App\Services\RaportSasService;
use Illuminate\Http\Request;
use App\Models\SchoolSetting;

class RaportController extends Controller
{
    public function printSts(Siswa $siswa, RaportStsService $service)
    {
        return $service->print($siswa);
    }

    public function printSas(Siswa $siswa, RaportSasService $service)
    {
        return $service->print($siswa);
    }

    public function printKartuUjian(Request $request)
    {
        $siswa = Siswa::with(['unit', 'rombel', 'rombel.ruangKelas', 'prestasis'])->findOrFail($request->siswa_id);
        $jenisUjian = $request->jenis_ujian; // sts or sas
        $semester = \App\Models\Semester::findOrFail($request->semester_id);
        $tahunAjaran = \App\Models\TahunAjaran::findOrFail($request->tahun_ajaran_id);
        
        $title = $jenisUjian == 'sts' ? 'SUMATIF TENGAH SEMESTER' : 'SUMATIF AKHIR SEMESTER';
        
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('print.kartu-ujian', [
            'siswa' => $siswa,
            'jenisUjian' => $jenisUjian,
            'semester' => $semester,
            'tahunAjaran' => $tahunAjaran,
            'title' => $title,
            'school_name' => SchoolSetting::get('school_name', 'Sekolah Kristen Kalam Kudus Sentani'),
            'principal_name' => SchoolSetting::get('principal_name', '..........................'),
            'principal_nip' => SchoolSetting::get('principal_nip', '-'),
        ]);
        return $pdf->stream("Kartu_Ujian_{$siswa->nis}.pdf");
    }
}
