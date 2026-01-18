@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-12">
    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow-xl overflow-hidden" x-data="{ step: 1 }">
        <div class="bg-blue-900 py-6 px-8 text-white">
            <h1 class="text-2xl font-bold">Formulir Pendaftaran Peserta Didik Baru</h1>
            <p class="opacity-80">Tahun Ajaran: {{ $setting->tahunAjaran->nama }} - Unit: {{ $setting->unit->nama }}</p>
        </div>

        <!-- Wizard Headers -->
        <div class="flex border-b">
            <div :class="{'text-blue-600 border-b-2 border-blue-600': step === 1, 'text-gray-500': step !== 1}" class="flex-1 py-4 text-center font-medium cursor-pointer">1. Data Siswa</div>
            <div :class="{'text-blue-600 border-b-2 border-blue-600': step === 2, 'text-gray-500': step !== 2}" class="flex-1 py-4 text-center font-medium cursor-pointer">2. Orang Tua</div>
            <div :class="{'text-blue-600 border-b-2 border-blue-600': step === 3, 'text-gray-500': step !== 3}" class="flex-1 py-4 text-center font-medium cursor-pointer">3. Berkas</div>
        </div>

        <form action="{{ route('ppdb.store') }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            <input type="hidden" name="ppdb_setting_id" value="{{ $setting->id }}">

            <!-- Step 1: Data Siswa -->
            <div x-show="step === 1" class="space-y-6">
                <h3 class="text-xl font-semibold border-b pb-2">Identitas Calon Siswa</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                            <option value="">Pilih</option>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tempat Lahir</label>
                        <input type="text" name="tempat_lahir" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tanggal Lahir</label>
                        <input type="date" name="tanggal_lahir" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Agama</label>
                        <select name="agama" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                             <option value="Islam">Islam</option>
                             <option value="Kristen">Kristen</option>
                             <option value="Katolik">Katolik</option>
                             <option value="Hindu">Hindu</option>
                             <option value="Buddha">Buddha</option>
                             <option value="Khonghucu">Khonghucu</option>
                        </select>
                    </div>
                    <div>
                         <label class="block text-sm font-medium text-gray-700">Asal Sekolah</label>
                         <input type="text" name="asal_sekolah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                     <label class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                     <textarea name="alamat" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" required></textarea>
                </div>
            </div>

            <!-- Step 2: Data Orang Tua -->
            <div x-show="step === 2" class="space-y-6" style="display: none;">
                <h3 class="text-xl font-semibold border-b pb-2">Identitas Orang Tua / Wali</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Ayah -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900">Ayah</h4>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Ayah</label>
                            <input type="text" name="nama_ayah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pekerjaan</label>
                            <input type="text" name="pekerjaan_ayah" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>
                    <!-- Ibu -->
                    <div class="space-y-4">
                        <h4 class="font-medium text-gray-900">Ibu</h4>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Ibu</label>
                            <input type="text" name="nama_ibu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pekerjaan</label>
                            <input type="text" name="pekerjaan_ibu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>
                </div>

                <div class="border-t pt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">No. HP Orang Tua (Aktif)</label>
                        <input type="tel" name="no_telepon_ortu" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">No. WhatsApp (Untuk Info)</label>
                        <input type="tel" name="no_wa" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700">Email Alamat</label>
                        <input type="email" name="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm" required>
                    </div>
                </div>
            </div>

            <!-- Step 3: Berkas -->
            <div x-show="step === 3" class="space-y-6" style="display: none;">
                <h3 class="text-xl font-semibold border-b pb-2">Upload Berkas Persyaratan</h3>
                <div class="bg-yellow-50 p-4 rounded text-sm text-yellow-800 mb-4">
                    Format file yang diterima: JPG, PNG, PDF. Maksimal 2MB per file.
                </div>

                <div class="space-y-4">
                    <!-- Akta -->
                    <div class="border p-4 rounded bg-gray-50 flex items-center justify-between">
                        <div>
                            <label class="font-medium text-gray-900">Akta Kelahiran</label>
                            <input type="hidden" name="dokumen[0][jenis]" value="akta_lahir">
                        </div>
                        <input type="file" name="dokumen[0][file]" class="text-sm" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>

                    <!-- KK -->
                    <div class="border p-4 rounded bg-gray-50 flex items-center justify-between">
                        <div>
                            <label class="font-medium text-gray-900">Kartu Keluarga (KK)</label>
                            <input type="hidden" name="dokumen[1][jenis]" value="kartu_keluarga">
                        </div>
                        <input type="file" name="dokumen[1][file]" class="text-sm" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>

                    <!-- Foto -->
                    <div class="border p-4 rounded bg-gray-50 flex items-center justify-between">
                        <div>
                            <label class="font-medium text-gray-900">Pas Foto (3x4)</label>
                            <input type="hidden" name="dokumen[2][jenis]" value="foto">
                        </div>
                        <input type="file" name="dokumen[2][file]" class="text-sm" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                    
                    @if(str_contains(strtolower($setting->unit->nama), 'smp'))
                    <!-- Ijazah for SMP -->
                    <div class="border p-4 rounded bg-gray-50 flex items-center justify-between">
                        <div>
                            <label class="font-medium text-gray-900">Ijazah / SKL SD</label>
                            <input type="hidden" name="dokumen[3][jenis]" value="ijazah">
                        </div>
                        <input type="file" name="dokumen[3][file]" class="text-sm" accept=".pdf,.jpg,.jpeg,.png" required>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="mt-8 flex justify-between pt-6 border-t">
                <button type="button" x-show="step > 1" @click="step--" class="bg-gray-500 text-white px-6 py-2 rounded hover:bg-gray-600">Kembali</button>
                <div x-show="step === 1"></div> <!-- Spacer -->
                
                <button type="button" x-show="step < 3" @click="step++" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">Lanjut &rarr;</button>
                
                <button type="submit" x-show="step === 3" class="bg-green-600 text-white px-8 py-2 rounded hover:bg-green-700 font-bold shadow-lg" style="display: none;">
                    Kirim Pendaftaran
                </button>
            </div>
        </form>
    </div>
</div>
<!-- Alpine JS for interactivity (assuming standard layout has it, if not need CDN) -->
<script src="//unpkg.com/alpinejs" defer></script>
@endsection
