<?php

namespace App\Http\Controllers;

use App\Models\Rapbs;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RapbsController extends Controller
{
    public function print(Rapbs $rapbs)
    {
        // Ensure only Disetujui can be printed? Or allow draft for preview?
        // User requested: "mencetak RAPBS yang telah di setujui"
        if ($rapbs->status !== 'disetujui') {
            abort(403, 'RAPBS belum disetujui.');
        }

        $rapbs->load(['unit', 'tahunAjaran', 'details', 'creator', 'approver']);
        $rapbs->calculateTotals(); // Ensure totals are fresh

        $pdf = Pdf::loadView('print.rapbs', compact('rapbs'));
        return $pdf->stream("RAPBS_{$rapbs->unit->nama}_{$rapbs->tahunAjaran->nama}.pdf");
    }
}
