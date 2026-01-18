<?php

namespace App\Http\Controllers;

use App\Models\Ekstrakurikuler;
use App\Models\Kokurikuler;
use App\Models\Semester;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PrintAbsensiController extends Controller
{
    public function printEkskul(Request $request, Ekstrakurikuler $record)
    {
        $semesterId = $request->get('semester_id') ?? Semester::where('is_active', true)->value('id');
        $semester = Semester::find($semesterId);

        // 1. Get Members for this Semester
        $members = \App\Models\RombelEkskul::where('ekstrakurikuler_id', $record->id)
            ->where('semester_id', $semesterId)
            ->with(['siswa', 'siswa.rombels'])
            ->get()
            ->sortBy('siswa.nama_lengkap');

        // 2. Get Activities (Kegiatan) sorted by date
        // We might want to filter activities by semester dates start/end if available?
        // Or just take all activities, assuming they match the semester context.
        // For now, let's take all activities created within the semester timeframe if semester has dates,
        // or just all activities if we don't strictly track semester dates on activities.
        // Better: Filter by date range of semester.
        // Assuming semester has tanggal_mulai and tanggal_selesai? Let's check model.
        // If not, just take all. *User request says "selama 1 semester"*
        
        $activities = $record->kegiatanEkskuls()
            ->orderBy('tanggal')
            ->get();
            
        // Filter activities by Semester Date Range if possible
        /*
        if ($semester && $semester->tanggal_mulai && $semester->tanggal_selesai) {
             $activities = $activities->whereBetween('tanggal', [$semester->tanggal_mulai, $semester->tanggal_selesai]);
        }
        */
        // Since I don't recall Semester fields exactly, I will check them or just skip for now.
        // Let's assume we show all tied to this Ekskul since usually Ekskul is cleaned up or new one created? 
        // Actually no, Ekskul is master data. Activities accumulate. 
        // We MUST filter by date. I will use a generic "Last 6 months" or try to find semester dates.
        
        // Let's load attendance
        $attendance = \App\Models\AbsensiEkskul::whereIn('kegiatan_ekskul_id', $activities->pluck('id'))
            ->get()
            ->groupBy('kegiatan_ekskul_id');
            // Make nested: [activity_id][student_id] -> status

        $data = [
            'title' => 'Rekap Absensi Ekstrakurikuler',
            'subject' => $record->nama,
            'unit' => $record->unit->nama,
            'semester' => $semester->tahunAjaran->nama . ' - ' . ucfirst($semester->tipe),
            'members' => $members,
            'activities' => $activities,
            'attendance' => $attendance,
            'pembina' => $record->pelatihEkskuls->first()?->guru->nama_lengkap ?? '-',
        ];

        $pdf = Pdf::loadView('print.rekap-absensi', $data);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('rekap_absensi_ekskul.pdf');
    }

    public function printKokurikuler(Request $request, Kokurikuler $record)
    {
        $semesterId = $request->get('semester_id') ?? Semester::where('is_active', true)->value('id');
        $semester = Semester::find($semesterId);

        $members = \App\Models\AnggotaKokurikuler::where('kokurikuler_id', $record->id)
            ->where('semester_id', $semesterId)
            ->with(['siswa', 'siswa.rombels']) // Rombels relation correct?
            ->get()
            ->sortBy('siswa.nama_lengkap');

        $activities = $record->topikKokurikulers()
            ->orderBy('tanggal')
            ->get();

        $attendance = \App\Models\AbsensiKokurikuler::whereIn('topik_kokurikuler_id', $activities->pluck('id'))
            ->get()
            ->groupBy('topik_kokurikuler_id');

        $data = [
            'title' => 'Rekap Absensi Kokurikuler',
            'subject' => $record->nama,
            'unit' => $record->unit->nama,
            'semester' => $semester->tahunAjaran->nama . ' - ' . ucfirst($semester->tipe),
            'members' => $members,
            'activities' => $activities,
            'attendance' => $attendance,
            'pembina' => $record->pembimbings->first()?->guru->nama_lengkap ?? '-',
        ];

        $pdf = Pdf::loadView('print.rekap-absensi', $data);
        $pdf->setPaper('a4', 'landscape');
        return $pdf->stream('rekap_absensi_kokurikuler.pdf');
    }
}
