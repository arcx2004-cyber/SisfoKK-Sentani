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
        // Get unit code to determine required documents
        $ppdbSetting = PpdbSetting::with('unit')->find($request->ppdb_setting_id);
        $unitCode = $ppdbSetting?->unit?->kode ?? '';
        
        // Base validation rules
        $rules = [
            'ppdb_setting_id' => 'required|exists:ppdb_settings,id',
            'jenis_pendaftaran' => 'required|in:Murid Baru,Mutasi',
            'tingkat' => 'required|string|max:50',
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
            // Document uploads
            'akta_kelahiran' => 'required|file|max:2048|mimes:pdf,jpg,jpeg,png',
            'kartu_keluarga' => 'required|file|max:2048|mimes:pdf,jpg,jpeg,png',
            'pas_foto'       => 'required|file|max:2048|mimes:jpg,jpeg,png',
        ];
        
        // Add ijazah requirement for SD and SMP
        if (in_array($unitCode, ['SD', 'SMP'])) {
            $rules['ijazah_skhu'] = 'required|file|max:2048|mimes:pdf,jpg,jpeg,png';
        } else {
            $rules['ijazah_skhu'] = 'nullable|file|max:2048|mimes:pdf,jpg,jpeg,png';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if PPDB is still open
        if (!$ppdbSetting || !$ppdbSetting->isOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Pendaftaran sudah ditutup'
            ], 400);
        }

        // Generate registration number
        $nomorPendaftaran = Pendaftaran::generateNomorPendaftaran();
        
        // Prepare data
        $data = $request->except(['akta_kelahiran', 'kartu_keluarga', 'ijazah_skhu', 'unit_code', 'pas_foto']);
        $data['nomor_pendaftaran'] = $nomorPendaftaran;
        $data['status'] = 'pending';

        // Save Pas Foto
        if ($request->hasFile('pas_foto')) {
            $data['pas_foto'] = $request->file('pas_foto')->store('pas-foto-ppdb', 'public');
        }

        $pendaftaran = Pendaftaran::create($data);

        // Upload documents
        $dokumenTypes = [
            'akta_kelahiran' => 'akta_lahir',
            'kartu_keluarga' => 'kartu_keluarga',
            'ijazah_skhu' => 'ijazah',
        ];

        foreach ($dokumenTypes as $fieldName => $jenisDokumen) {
            if ($request->hasFile($fieldName)) {
                $file = $request->file($fieldName);
                $path = $file->store('ppdb/dokumen/' . $pendaftaran->id, 'public');
                
                DokumenPendaftaran::create([
                    'pendaftaran_id' => $pendaftaran->id,
                    'jenis_dokumen' => $jenisDokumen,
                    'nama_file' => $file->getClientOriginalName(),
                    'path' => $path,
                ]);
            }
        }
        
        // Also save Pas Foto as Dokumen (optional, but good for consistency/backup)
        if ($request->hasFile('pas_foto')) {
             DokumenPendaftaran::create([
                'pendaftaran_id' => $pendaftaran->id,
                'jenis_dokumen' => 'foto',
                'nama_file' => $request->file('pas_foto')->getClientOriginalName(),
                'path' => $data['pas_foto'],
            ]);
        }

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
