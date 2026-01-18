<?php

use App\Http\Controllers\Api\PublicApiController;
use App\Http\Controllers\Api\PpdbApiController;
use Illuminate\Support\Facades\Route;

// Public API Routes (no authentication required)
Route::prefix('v1')->group(function () {
    // CMS & Content
    Route::get('/settings', [PublicApiController::class, 'settings']);
    Route::get('/menus', [PublicApiController::class, 'menus']);
    Route::get('/sliders', [PublicApiController::class, 'sliders']);
    Route::get('/units', [PublicApiController::class, 'units']);
    Route::get('/units/{kode}', [PublicApiController::class, 'unitDetail']);
    
    // News
    Route::get('/news', [PublicApiController::class, 'news']);
    Route::get('/news/{slug}', [PublicApiController::class, 'newsDetail']);
    
    // Gallery
    Route::get('/galleries', [PublicApiController::class, 'galleries']);
    
    // Kegiatan
    Route::get('/kegiatan', [PublicApiController::class, 'kegiatan']);
    Route::get('/kegiatan/{slug}', [PublicApiController::class, 'kegiatanDetail']);
    
    // Pages
    Route::get('/pages/{slug}', [PublicApiController::class, 'page']);
    
    // PPDB
    Route::get('/ppdb/info', [PublicApiController::class, 'ppdbInfo']);
    Route::post('/ppdb/register', [PpdbApiController::class, 'store']);
    Route::post('/ppdb/{pendaftaranId}/dokumen', [PpdbApiController::class, 'uploadDokumen']);
    Route::get('/ppdb/status/{nomorPendaftaran}', [PpdbApiController::class, 'checkStatus']);
});
