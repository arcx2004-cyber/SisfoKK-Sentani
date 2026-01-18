<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use App\Models\DokumenPendaftaran;
use App\Models\PpdbSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PpdbApiController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ppdb_setting_id' => 'required|exists:ppdb_settings,id',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'agama' => 'nullable|string|max:50',
            'alamat' => 'required|string',
            'asal_sekolah' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'no_wa' => 'required|string|max:20',
            'nama_ayah' => 'required|string|max:255',
            'pekerjaan_ayah' => 'nullable|string|max:255',
            'nama_ibu' => 'required|string|max:255',
            'pekerjaan_ibu' => 'nullable|string|max:255',
            'no_telepon_ortu' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if PPDB is still open
        $ppdbSetting = PpdbSetting::find($request->ppdb_setting_id);
        if (!$ppdbSetting->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran sudah ditutup'
            ], 400);
        }

        // Generate registration number
        $nomorPendaftaran = Pendaftaran::generateNomorPendaftaran();

        $pendaftaran = Pendaftaran::create([
            ...$request->except('dokumen'),
            'nomor_pendaftaran' => $nomorPendaftaran,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pendaftaran berhasil',
            'data' => [
                'nomor_pendaftaran' => $nomorPendaftaran,
                'pendaftaran_id' => $pendaftaran->id
            ]
        ], 201);
    }

    public function uploadDokumen(Request $request, int $pendaftaranId): JsonResponse
    {
        $pendaftaran = Pendaftaran::findOrFail($pendaftaranId);

        $validator = Validator::make($request->all(), [
            'jenis_dokumen' => 'required|in:akta_lahir,kartu_keluarga,ijazah,foto,lainnya',
            'file' => 'required|file|max:5120|mimes:pdf,jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $file = $request->file('file');
        $path = $file->store('ppdb/dokumen/' . $pendaftaranId, 'public');

        $dokumen = DokumenPendaftaran::create([
            'pendaftaran_id' => $pendaftaranId,
            'jenis_dokumen' => $request->jenis_dokumen,
            'nama_file' => $file->getClientOriginalName(),
            'path' => $path,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Dokumen berhasil diupload',
            'data' => $dokumen
        ], 201);
    }

    public function checkStatus(string $nomorPendaftaran): JsonResponse
    {
        $pendaftaran = Pendaftaran::where('nomor_pendaftaran', $nomorPendaftaran)
            ->with('dokumenPendaftarans', 'ppdbSetting.unit')
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $pendaftaran
        ]);
    }
}
