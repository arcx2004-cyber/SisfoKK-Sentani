<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\SchoolSetting;
use App\Models\Slider;
use App\Models\News;
use App\Models\Gallery;
use App\Models\Kegiatan;
use App\Models\Page;
use App\Models\Unit;
use App\Models\PpdbSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicApiController extends Controller
{
    public function settings(): JsonResponse
    {
        $settings = SchoolSetting::all()->pluck('value', 'key');
        return response()->json([
            'success' => true,
            'data' => $settings
        ]);
    }

    public function menus(): JsonResponse
    {
        $menus = Menu::getMenuTree();
        return response()->json([
            'success' => true,
            'data' => $menus
        ]);
    }

    public function sliders(): JsonResponse
    {
        $sliders = Slider::active()->get();
        return response()->json([
            'success' => true,
            'data' => $sliders
        ]);
    }

    public function news(Request $request): JsonResponse
    {
        $query = News::published()->orderBy('published_at', 'desc');
        
        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        $news = $query->get();
        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function newsDetail(string $slug): JsonResponse
    {
        $news = News::where('slug', $slug)->published()->firstOrFail();
        $news->incrementViews();

        return response()->json([
            'success' => true,
            'data' => $news
        ]);
    }

    public function galleries(): JsonResponse
    {
        $galleries = Gallery::active()->with('photos')->get();
        return response()->json([
            'success' => true,
            'data' => $galleries
        ]);
    }

    public function kegiatan(Request $request): JsonResponse
    {
        $query = Kegiatan::published()->orderBy('tanggal_mulai', 'desc');
        
        if ($request->has('upcoming')) {
            $query->upcoming();
        }

        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        $kegiatan = $query->get();
        return response()->json([
            'success' => true,
            'data' => $kegiatan
        ]);
    }

    public function kegiatanDetail(string $slug): JsonResponse
    {
        $kegiatan = Kegiatan::where('slug', $slug)->published()->firstOrFail();
        return response()->json([
            'success' => true,
            'data' => $kegiatan
        ]);
    }

    public function page(string $slug): JsonResponse
    {
        $page = Page::where('slug', $slug)->published()->firstOrFail();
        return response()->json([
            'success' => true,
            'data' => $page
        ]);
    }

    public function units(): JsonResponse
    {
        $units = Unit::where('is_active', true)->orderBy('urutan')->get();
        return response()->json([
            'success' => true,
            'data' => $units
        ]);
    }

    public function unitDetail(string $kode): JsonResponse
    {
        $unit = Unit::where('kode', strtoupper($kode))
            ->where('is_active', true)
            ->withCount(['gurus' => function ($query) {
                $query->where('is_active', true);
            }, 'siswas' => function ($query) {
                $query->where('status', 'aktif');
            }])
            ->with(['gurus' => function ($query) {
                $query->where('is_active', true)->orderBy('nama_lengkap')->limit(12);
            }])
            ->first();

        if (!$unit) {
            return response()->json([
                'success' => false,
                'message' => 'Unit tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $unit
        ]);
    }

    public function ppdbInfo(): JsonResponse
    {
        $ppdbSettings = PpdbSetting::with('unit', 'tahunAjaran')
            ->orderBy('is_active', 'desc')
            ->get()
            ->map(function ($setting) {
                return [
                    'id' => $setting->id,
                    'unit' => $setting->unit ? [
                        'id' => $setting->unit->id,
                        'nama' => $setting->unit->nama,
                        'kode' => $setting->unit->kode,
                    ] : null,
                    'tahun_ajaran' => $setting->tahunAjaran ? [
                        'id' => $setting->tahunAjaran->id,
                        'nama' => $setting->tahunAjaran->nama,
                    ] : null,
                    'is_active' => $setting->is_active,
                    'is_open' => $setting->isOpen(),
                    'tanggal_buka' => $setting->tanggal_buka?->format('Y-m-d'),
                    'tanggal_buka_formatted' => $setting->tanggal_buka?->format('d F Y'),
                    'tanggal_tutup' => $setting->tanggal_tutup?->format('Y-m-d'),
                    'tanggal_tutup_formatted' => $setting->tanggal_tutup?->format('d F Y'),
                    'biaya_pendaftaran' => $setting->biaya_pendaftaran,
                    'alur_pendaftaran' => $setting->alur_pendaftaran,
                    'persyaratan' => $setting->persyaratan,
                ];
            });

        // Check if any PPDB is currently open
        $anyOpen = $ppdbSettings->contains('is_open', true);

        return response()->json([
            'success' => true,
            'is_any_open' => $anyOpen,
            'data' => $ppdbSettings
        ]);
    }
}
