@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-gray-800 py-4 px-6 flex justify-between items-center">
            <h2 class="text-xl font-bold text-white">Hasil PPDB</h2>
            <a href="{{ route('ppdb.check-status') }}" class="text-gray-300 hover:text-white text-sm">Cek Lainnya</a>
        </div>
        
        <div class="p-8 text-center">
            <h3 class="text-2xl font-bold mb-2">{{ $pendaftaran->nama_lengkap }}</h3>
            <p class="text-gray-500 mb-6">No. Pendaftaran: {{ $pendaftaran->no_pendaftaran }}</p>

            @if($pendaftaran->status === 'diterima')
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-8 rounded mb-6">
                    <h1 class="text-4xl font-bold mb-2">SELAMAT!</h1>
                    <p class="text-xl">Anda dinyatakan <span class="font-bold">DITERIMA</span></p>
                    <p class="mt-2 text-sm">di {{ $pendaftaran->ppdbSetting->unit->nama }}</p>
                </div>
                <p class="text-gray-600">Simpan bukti ini dan lakukan daftar ulang sesuai jadwal yang ditentukan.</p>
                <!-- Maybe add "Cetak Bukti" button logic later -->
            @elseif($pendaftaran->status === 'ditolak')
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-8 rounded mb-6">
                    <h1 class="text-3xl font-bold mb-2">MOHON MAAF</h1>
                    <p class="text-lg">Anda dinyatakan <span class="font-bold">TIDAK DITERIMA</span></p>
                </div>
                <p class="text-gray-600">Jangan patah semangat. Tetap terus belajar!</p>
            @else
                <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-8 rounded mb-6">
                    <h1 class="text-3xl font-bold mb-2">MENUNGGU</h1>
                    <p class="text-lg">Status pendaftaran Anda saat ini:</p>
                    <p class="text-2xl font-bold mt-2 uppercase">{{ $pendaftaran->status }}</p>
                </div>
                <p class="text-gray-600">Mohon menunggu pengumuman selanjutnya.</p>
            @endif
        </div>
        
        @if($pendaftaran->catatan_admin)
        <div class="bg-gray-50 px-6 py-4 border-t">
            <p class="text-sm font-bold text-gray-500 mb-1">Catatan Panitia:</p>
            <p class="text-gray-700">{{ $pendaftaran->catatan_admin }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
