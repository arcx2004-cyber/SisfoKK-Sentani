<?php

namespace App\Http\Controllers;

use App\Models\PpdbSetting;
use App\Models\Pendaftaran;
use App\Models\DokumenPendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PpdbController extends Controller
{
    public function index()
    {
        // Get active PPDB Setting
        $setting = PpdbSetting::with('unit')->where('is_active', true)->first();
        
        if (!$setting) {
            return view('ppdb.closed'); // Need to create this view
        }

        return view('ppdb.register', compact('setting'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ppdb_setting_id' => 'required|exists:ppdb_settings,id',
            'nama_lengkap' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string',
            'tanggal_lahir' => 'required|date',
            'agama' => 'required|string',
            'alamat' => 'required|string',
            'asal_sekolah' => 'nullable|string',
            'email' => 'required|email',
            'no_wa' => 'required|string',
            'nama_ayah' => 'required|string',
            'pekerjaan_ayah' => 'nullable|string',
            'nama_ibu' => 'required|string',
            'pekerjaan_ibu' => 'nullable|string',
            'no_telepon_ortu' => 'required|string',
            // File validations
            'dokumen.*.jenis' => 'required|string',
            'dokumen.*.file' => 'required|file|max:2048', // 2MB max
        ]);

        return DB::transaction(function () use ($request, $validated) {
            // Create Pendaftaran
            // Observer will handle no_pendaftaran generation
            $pendaftaran = Pendaftaran::create([
                'ppdb_setting_id' => $validated['ppdb_setting_id'],
                'nama_lengkap' => $validated['nama_lengkap'],
                'jenis_kelamin' => $validated['jenis_kelamin'],
                'tempat_lahir' => $validated['tempat_lahir'],
                'tanggal_lahir' => $validated['tanggal_lahir'],
                'agama' => $validated['agama'],
                'alamat' => $validated['alamat'],
                'asal_sekolah' => $validated['asal_sekolah'],
                'email' => $validated['email'],
                'no_wa' => $validated['no_wa'],
                'nama_ayah' => $validated['nama_ayah'],
                'pekerjaan_ayah' => $validated['pekerjaan_ayah'],
                'nama_ibu' => $validated['nama_ibu'],
                'pekerjaan_ibu' => $validated['pekerjaan_ibu'],
                'no_telepon_ortu' => $validated['no_telepon_ortu'],
                'status' => 'pending',
            ]);

            // Handle Files
            if ($request->hasFile('dokumen')) {
                foreach ($request->file('dokumen') as $key => $file) {
                    // Assuming frontend sends array of files keyed by 'jenis' or similar structure
                    // Actually, HTML file inputs usually names like dokumen[0][file]
                    // Let's assume request structure: dokumen[0][jenis], dokumen[0][file]
                    $jenis = $request->input("dokumen.$key.jenis");
                    
                    if ($file->isValid()) {
                        $path = $file->store('dokumen-ppdb', 'public');
                        
                        DokumenPendaftaran::create([
                            'pendaftaran_id' => $pendaftaran->id,
                            'jenis_dokumen' => $jenis,
                            'nama_file' => $file->getClientOriginalName(),
                            'path' => $path,
                        ]);
                    }
                }
            }
            
            return redirect()->route('ppdb.success', ['nomor' => $pendaftaran->no_pendaftaran]);
        });
    }

    public function success($nomor)
    {
        $pendaftaran = Pendaftaran::where('nomor_pendaftaran', $nomor)->firstOrFail();
        return view('ppdb.success', compact('pendaftaran'));
    }

    public function checkStatus()
    {
        return view('ppdb.check-status');
    }

    public function processCheckStatus(Request $request)
    {
        $request->validate([
            'nomor_pendaftaran' => 'required|string',
        ]);

        $pendaftaran = Pendaftaran::with('ppdbSetting.unit')
            ->where('nomor_pendaftaran', $request->nomor_pendaftaran)
            ->first();

        if (!$pendaftaran) {
            return back()->with('error', 'Nomor Pendaftaran tidak ditemukan.');
        }

        return view('ppdb.status_result', compact('pendaftaran'));
    }
}
