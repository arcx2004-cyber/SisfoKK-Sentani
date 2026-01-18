<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Ekstrakurikuler Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <x-heroicon-o-trophy class="w-5 h-5 mr-2 text-primary-600"/>
                Ekstrakurikuler
            </h2>
            @if(count($ekskuls) > 0)
                <div class="space-y-4">
                    @foreach($ekskuls as $item)
                        <div class="flex items-start p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600">
                                    <span class="text-sm font-bold">{{ substr($item->ekstrakurikuler->nama, 0, 1) }}</span>
                                </span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">{{ $item->ekstrakurikuler->nama }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ $item->ekstrakurikuler->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                                @if($item->nilaiEkskul)
                                    <div class="mt-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        Nilai: {{ $item->nilaiEkskul->predikat }} ({{ $item->nilaiEkskul->keterangan }})
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 text-sm italic">
                    Belum mengikuti ekstrakurikuler.
                </div>
            @endif
        </div>

        <!-- Kokurikuler Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <x-heroicon-o-academic-cap class="w-5 h-5 mr-2 text-primary-600"/>
                Kokurikuler
            </h2>
            @if(count($kokurikulers) > 0)
                <div class="space-y-4">
                    @foreach($kokurikulers as $item)
                        <div class="flex items-start p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <div class="flex-shrink-0">
                                <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-purple-100 text-purple-600">
                                    <span class="text-sm font-bold">{{ substr($item->kokurikuler->nama, 0, 1) }}</span>
                                </span>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-900">{{ $item->kokurikuler->nama }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ $item->kokurikuler->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                                <div class="mt-2 text-xs text-gray-500">
                                    Pembimbing: {{ $item->pembimbing->nama_lengkap ?? '-' }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 text-sm italic">
                    Belum mengikuti kokurikuler.
                </div>
            @endif
        </div>

    </div>
</x-filament-panels::page>
