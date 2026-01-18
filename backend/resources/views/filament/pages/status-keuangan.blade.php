<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- SPP Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <x-heroicon-o-calendar class="w-5 h-5 mr-2 text-primary-600"/>
                SPP Tahun Ini
            </h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-3 py-2">Bulan</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2 text-right">Nominal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($sppData as $spp)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-medium text-gray-900">{{ $spp['bulan'] }}</td>
                                <td class="px-3 py-2">
                                    @if($spp['status'] == 'Lunas')
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Lunas</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Belum</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-right text-gray-600">{{ $spp['nominal'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kegiatan Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <x-heroicon-o-banknotes class="w-5 h-5 mr-2 text-primary-600"/>
                Kegiatan Tahunan
            </h2>
             <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="px-3 py-2">Kegiatan</th>
                            <th class="px-3 py-2">Status</th>
                            <th class="px-3 py-2 text-right">Dibayar / Tagihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($kegiatanData as $kegiatan)
                            <tr class="hover:bg-gray-50">
                                <td class="px-3 py-2 font-medium text-gray-900">{{ $kegiatan['kegiatan'] }}</td>
                                <td class="px-3 py-2">
                                     @if($kegiatan['status'] == 'Lunas')
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Lunas</span>
                                    @elseif($kegiatan['status'] == 'Sebagian')
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Sebagian</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Belum</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-right">
                                    <div class="text-xs text-gray-500">
                                        <span class="text-gray-900 font-semibold">{{ $kegiatan['terbayar'] }}</span> 
                                        / {{ $kegiatan['tagihan'] }}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-3 py-4 text-center text-gray-500 italic">Tidak ada tagihan kegiatan tahun ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-filament-panels::page>
