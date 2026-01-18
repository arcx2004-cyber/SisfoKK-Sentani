<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\Semester;
use App\Models\NilaiEvaluasi;
use App\Models\NilaiSiswa;
use Barryvdh\DomPDF\Facade\Pdf; // Ensure dompdf is installed or use view

class RaportStsService
{
    public function print(Siswa $siswa)
    {
        $semester = Semester::getActive();
        $unit = $siswa->unit;
        
        // Logical check for Unit (SD vs SMP) - formats differ
        $viewName = $unit->nama === 'SD' ? 'print.rapor-sts-sd' : 'print.rapor-sts-smp';

        // 1. Get Academic Grades (Evaluasi - STS)
        // Adjust filtered model id based on database seeder/constants
        $stsModelId = \App\Models\ModelPenilaian::where('kode', 'STS')->value('id');
        
        $nilaiAkademik = NilaiEvaluasi::where('siswa_id', $siswa->id)
            ->where('semester_id', $semester->id)
            ->where('model_penilaian_id', $stsModelId)
            ->whereHas('mataPelajaran', function ($query) use ($unit) {
                $query->where('unit_id', $unit->id);
            })
            ->with('mataPelajaran')
            ->get();
            
        // 2. Extra Data (Attendance, etc if needed)
        // ...

        $pdf = Pdf::loadView($viewName, [
            'siswa' => $siswa,
            'semester' => $semester,
            'nilaiAkademik' => $nilaiAkademik,
            'tanggal' => now()->format('d F Y'),
        ]);

        return $pdf->stream("Raport_STS_{$siswa->nis}.pdf");
    }
}
