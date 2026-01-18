<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\Semester;
use App\Models\TahunAjaran;
use App\Models\MataPelajaran;
use App\Models\NilaiSiswa;
use App\Models\SchoolSetting;
use Barryvdh\DomPDF\Facade\Pdf;

class RaportSasService
{
    public function print(Siswa $siswa)
    {
        $semester = Semester::getActive();
        $tahunAjaran = $semester->tahunAjaran;
        $unit = $siswa->unit;
        
        // Get active rombel for this siswa
        $rombel = $siswa->rombels()
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->first();
        
        if (!$rombel) {
            $rombel = $siswa->rombels()->first();
        }

        $rombelId = $rombel?->id;
        $semId = $semester->id;
        $taId = $tahunAjaran->id;

        // Eager load relations
        $siswa->load(['prestasis', 'kesehatans', 'dataTubuhs', 'catatanAkhirs', 'rombelEkskuls.kegiatan']);

        // Fetch Mapels for this unit
        $mapels = MataPelajaran::where('unit_id', $unit->id)
            ->where('is_active', true)
            ->orderBy('urutan')
            ->get();

        $nilaiData = [];

        foreach ($mapels as $mapel) {
            // Get all NilaiSiswa for this siswa, semester, linked to this mapel's CPs
            $nilaiSiswas = NilaiSiswa::where('siswa_id', $siswa->id)
                ->where('semester_id', $semId)
                ->whereHas('capaianPembelajaran', function ($q) use ($mapel) {
                    $q->where('mata_pelajaran_id', $mapel->id);
                })
                ->with('capaianPembelajaran')
                ->get();

            // Calculate average nilai
            $avgNilai = $nilaiSiswas->count() > 0 
                ? round($nilaiSiswas->avg('nilai'), 0) 
                : null;

            // Combine deskripsi from all CPs
            $deskripsiCombined = $nilaiSiswas
                ->filter(fn($ns) => !empty($ns->deskripsi))
                ->pluck('deskripsi')
                ->unique()
                ->implode(' ');

            // Get Guru for this Mapel in this Rombel/Semester
            $guruMengajar = \App\Models\GuruMengajar::where('mata_pelajaran_id', $mapel->id)
                ->where('rombel_id', $rombelId)
                ->where('semester_id', $semId)
                ->with('guru')
                ->first();

            $namaGuru = $guruMengajar?->guru?->nama_lengkap ?? '-';

            $nilaiData[] = [
                'mapel' => $mapel,
                'nilai' => $avgNilai,
                'deskripsi' => $deskripsiCombined ?: '-',
                'nama_guru' => $namaGuru,
            ];
        }

        // Fetch Absensi from CatatanAkhir
        $catatanAkhir = $siswa->catatanAkhirs->where('semester_id', $semId)->first();
        $sakit = $catatanAkhir?->sakit ?? 0;
        $ijin = $catatanAkhir?->izin ?? 0;
        $alpa = $catatanAkhir?->alpha ?? 0;

        // Fetch Data Tubuh for both semesters of this TA
        $semesterGanjil = Semester::where('tahun_ajaran_id', $taId)->where('tipe', 'ganjil')->first();
        $semesterGenap = Semester::where('tahun_ajaran_id', $taId)->where('tipe', 'genap')->first();
        
        $dataTubuhGanjil = $siswa->dataTubuhs->where('semester_id', $semesterGanjil?->id)->first();
        $dataTubuhGenap = $siswa->dataTubuhs->where('semester_id', $semesterGenap?->id)->first();

        // Fetch Wali Kelas for footer
        $waliKelas = \App\Models\WaliKelas::where('rombel_id', $rombelId)
            ->where('semester_id', $semId)
            ->with('guru')
            ->first();
        
        $namaWaliKelas = $waliKelas?->guru?->nama_lengkap ?? '-';

        // School Settings (for Kepala Sekolah name)
        $settings = SchoolSetting::all()->pluck('value', 'key');

        // Ekstrakurikuler with grades
        $ekskul = $siswa->rombelEkskuls;

        // Determine view based on unit
        $viewName = $unit->nama === 'SD' ? 'print.rapor-sas-sd' : 'print.rapor-sas-smp';

        $pdf = Pdf::loadView($viewName, compact(
            'siswa', 'rombel', 'tahunAjaran', 'semester', 'unit',
            'nilaiData', 'catatanAkhir', 'sakit', 'ijin', 'alpa',
            'dataTubuhGanjil', 'dataTubuhGenap',
            'namaWaliKelas', 'settings', 'ekskul'
        ));

        return $pdf->stream("Raport_SAS_{$siswa->nis}.pdf");
    }
}
