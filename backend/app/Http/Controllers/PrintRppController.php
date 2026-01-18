<?php

namespace App\Http\Controllers;

use App\Models\Rpp;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PrintRppController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Rpp $record)
    {
        if ($record->status !== 'approved') {
            abort(403, 'RPP belum disetujui.');
        }

        $pdf = Pdf::loadView('print.rpp', [
            'rpp' => $record,
            'guru' => $record->guru,
            'mapel' => $record->capaianPembelajaran->mataPelajaran,
            'cp' => $record->capaianPembelajaran,
        ]);

        return $pdf->stream('RPP-' . $record->capaianPembelajaran->mataPelajaran->nama . '.pdf');
    }
}
