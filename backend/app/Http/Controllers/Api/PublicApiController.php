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

    public function documents(Request $request): JsonResponse
    {
        $query = \App\Models\Document::where('is_public', true)->orderBy('created_at', 'desc');
        
        if ($request->has('type') && $request->type !== 'all') {
            $query->where('type', $request->type);
        }

        $documents = $query->get();
        return response()->json([
            'success' => true,
            'data' => $documents
        ]);
    }

    public function mading(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 12);
        $mading = \App\Models\Mading::where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $mading
        ]);
    }

    public function prestasi(Request $request): JsonResponse
    {
        $limit = $request->input('limit', 9);
        $prestasi = \App\Models\Prestasi::where('is_public', true)
            ->with(['siswa' => function($q) {
                $q->select('id', 'nama_lengkap', 'unit_id');
            }, 'siswa.unit' => function($q) {
                $q->select('id', 'nama');
            }])
            ->orderBy('tanggal', 'desc')
            ->paginate($limit);

        return response()->json([
            'success' => true,
            'data' => $prestasi
        ]);
    }

    public function sambutans(): JsonResponse
    {
        $sambutans = \App\Models\Sambutan::where('is_active', true)->orderBy('urutan')->get();
        return response()->json([
            'success' => true,
            'data' => $sambutans
        ]);
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            \App\Models\Message::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'subject' => $request->subject,
                'content' => $request->content,
                'is_read' => false
            ]);

            $admins = User::all();
            foreach ($admins as $admin) {
                Notification::make()
                    ->title('Pesan Baru dari ' . $request->name)
                    ->body($request->subject)
                    ->icon('heroicon-o-inbox')
                    ->success()
                    ->actions([
                        Action::make('Lihat')
                            ->button()
                            ->url('/admin/messages') 
                            ->markAsRead(),
                    ])
                    ->sendToDatabase($admin);
            }

            return response()->json([
                'success' => true,
                'message' => 'Pesan berhasil dikirim. Kami akan segera menghubungi Anda.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
