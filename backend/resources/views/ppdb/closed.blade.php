@extends('layouts.app') {{-- Assuming a main layout exists, otherwise simple HTML --}}

@section('content')
<div class="container mx-auto px-4 py-16 text-center">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-lg mx-auto">
        <h1 class="text-3xl font-bold text-red-600 mb-4">Pendaftaran Ditutup</h1>
        <p class="text-gray-600 mb-6">Mohon maaf, saat ini sedang tidak ada gelombang Pendaftaran Peserta Didik Baru (PPDB) yang dibuka.</p>
        <p class="text-gray-500">Silakan hubungi sekolah untuk informasi lebih lanjut.</p>
    </div>
</div>
@endsection
