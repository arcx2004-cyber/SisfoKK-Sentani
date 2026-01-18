@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16 text-center">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-2xl mx-auto">
        <div class="mb-6">
            <svg class="mx-auto h-20 w-20 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <h1 class="text-3xl font-bold text-green-600 mb-2">Pendaftaran Berhasil!</h1>
        <p class="text-gray-600 mb-8">Terima kasih telah mendaftar. Data Anda telah kami terima.</p>
        
        <div class="bg-gray-100 p-6 rounded-lg mb-8">
            <p class="text-sm text-gray-500 mb-1">Nomor Pendaftaran Anda:</p>
            <h2 class="text-4xl font-mono font-bold text-blue-600 tracking-wider select-all">{{ $pendaftaran->no_pendaftaran }}</h2>
            <p class="text-xs text-red-500 mt-2 font-semibold">Simpan Nomor Pendaftaran ini untuk mengecek status kelulusan!</p>
        </div>

        <div class="mt-8">
            <a href="{{ route('ppdb.check-status') }}" class="text-blue-600 hover:text-blue-800 font-medium hover:underline">
                Cek Status Pendaftaran &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
