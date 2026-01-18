@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="bg-blue-600 py-4 px-6">
            <h2 class="text-xl font-bold text-white">Cek Status Pendaftaran</h2>
        </div>
        <div class="p-6">
            @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            @endif

            <form action="{{ route('ppdb.process-check') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="nomor_pendaftaran" class="block text-gray-700 text-sm font-bold mb-2">Nomor Pendaftaran</label>
                    <input type="text" name="nomor_pendaftaran" id="nomor_pendaftaran" 
                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline uppercase"
                        placeholder="Contoh: PPDB-2024-SD-001" required>
                </div>
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full">
                        Cek Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
