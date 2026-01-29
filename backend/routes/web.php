<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/webadmin/login');
});

// Public PPDB Routes
Route::get('/ppdb/daftar', [App\Http\Controllers\PpdbController::class, 'index'])->name('ppdb.index');
Route::post('/ppdb/store', [App\Http\Controllers\PpdbController::class, 'store'])->name('ppdb.store');
Route::get('/ppdb/sukses/{nomor}', [App\Http\Controllers\PpdbController::class, 'success'])->name('ppdb.success');
Route::get('/ppdb/cek-status', [App\Http\Controllers\PpdbController::class, 'checkStatus'])->name('ppdb.check-status');
Route::post('/ppdb/cek-status', [App\Http\Controllers\PpdbController::class, 'processCheckStatus'])->name('ppdb.process-check-status');
Route::get('/siswa/print-all', [App\Http\Controllers\SiswaPrintController::class, 'printAll'])->name('siswa.print-all')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/raport/sts/{siswa}', [\App\Http\Controllers\RaportController::class, 'printSts'])->name('raport.sts');
    Route::get('/raport/sas/{siswa}', [\App\Http\Controllers\RaportController::class, 'printSas'])->name('raport.sas');
    Route::get('/kartu-ujian/print', [\App\Http\Controllers\RaportController::class, 'printKartuUjian'])->name('cetak.kartu.ujian');
    Route::get('/rapbs/{rapbs}/print', [\App\Http\Controllers\RapbsController::class, 'print'])->name('rapbs.print');
    Route::get('/cetak-kartu-pelajar/{siswa}', function (\App\Models\Siswa $siswa) {
        if (!auth()->user() || (auth()->user()->hasRole('siswa') && auth()->user()->siswa->id !== $siswa->id && !auth()->user()->can('view_any_siswa'))) {
             abort(403);
        }
        return view('print.kartu-pelajar', ['student' => $siswa]);
    })->name('print.kartu-pelajar');
    
    Route::get('/print/laporan-spp', [\App\Http\Controllers\PrintController::class, 'printLaporanSpp'])->name('print.laporan-spp');
    Route::get('/print/laporan-kegiatan', [\App\Http\Controllers\PrintController::class, 'printLaporanKegiatan'])->name('print.laporan-kegiatan');
});

Route::get('/print/rapor-sts', [App\Http\Controllers\RaporStsController::class, 'print'])->name('print.rapor-sts');
Route::get('/print/rapor-sas', [App\Http\Controllers\RaporStsController::class, 'printSas'])->name('print.rapor-sas');
Route::get('/rpp/{record}/print', [App\Http\Controllers\PrintRppController::class, '__invoke'])->name('rpp.print');
Route::get('/print/ekskul/{record}', [\App\Http\Controllers\PrintAbsensiController::class, 'printEkskul'])->name('print.absensi.ekskul');
Route::get('/print/kokurikuler/{record}', [\App\Http\Controllers\PrintAbsensiController::class, 'printKokurikuler'])->name('print.absensi.kokurikuler');
Route::get('/print/ekskul/{record}', [\App\Http\Controllers\PrintAbsensiController::class, 'printEkskul'])->name('print.absensi.ekskul');
Route::get('/print/kokurikuler/{record}', [\App\Http\Controllers\PrintAbsensiController::class, 'printKokurikuler'])->name('print.absensi.kokurikuler');
