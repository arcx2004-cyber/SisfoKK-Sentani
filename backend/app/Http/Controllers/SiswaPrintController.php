<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Semester;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class SiswaPrintController extends Controller
{
    public function printAll()
    {
        $user = auth()->user();
        $query = Siswa::query();

        // 1. Scoping Limit
        if ($user->hasAnyRole(['kepala_sekolah', 'kepsek']) && $user->guru && $user->guru->unit_id) {
             $query->where('unit_id', $user->guru->unit_id);
             $unitName = $user->guru->unit->nama;
        } else {
            $unitName = 'Semua Unit';
        }

        // 2. Fetch Data with Unit and Active Rombel
        $siswas = $query->with(['unit', 'rombels' => function($q) {
            $activeSemester = Semester::getActive();
            if ($activeSemester) {
                $q->where('tahun_ajaran_id', $activeSemester->tahun_ajaran_id);
            }
        }])
        ->orderBy('nama_lengkap')
        ->get();

        // 3. Group by Rombel
        $grouped = $siswas->groupBy(function($siswa) {
            return $siswa->getCurrentRombel()?->nama ?? 'Belum Ada Rombel';
        });

        // 4. Calculate Recap
        $recap = [];
        $totalSiswa = $siswas->count();
        $totalL = 0;
        $totalP = 0;
        
        // Recap per Rombel
        foreach ($grouped as $rombelName => $list) {
            $l = $list->where('jenis_kelamin', 'L')->count();
            $p = $list->where('jenis_kelamin', 'P')->count();
            $recap['rombels'][$rombelName] = [
                'L' => $l,
                'P' => $p,
                'total' => $list->count()
            ];
            $totalL += $l;
            $totalP += $p;
        }

        // Recap Agama
        $agamas = $siswas->groupBy('agama')->map->count();

        $pdf = Pdf::loadView('print.siswa-all', [
            'grouped' => $grouped,
            'recap' => $recap,
            'agamas' => $agamas,
            'totalSiswa' => $totalSiswa,
            'totalL' => $totalL,
            'totalP' => $totalP,
            'unitName' => $unitName,
            'tahunAjaran' => Semester::getActive()?->tahunAjaran->nama ?? '-'
        ]);

        return $pdf->stream('data-siswa.pdf');
    }
}
