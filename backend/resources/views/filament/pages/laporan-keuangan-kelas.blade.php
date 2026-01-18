<x-filament-panels::page>
    @if(!$rombel)
        <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300" role="alert">
            <span class="font-medium">Perhatian!</span> Anda belum terdaftar sebagai Wali Kelas untuk Semester/Tahun Ajaran aktif ini.
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $rombel->nama }}</h2>
                    <p class="text-sm text-gray-500">Wali Kelas: {{ auth()->user()->guru->nama_lengkap ?? auth()->user()->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-gray-400 uppercase tracking-wider">Tahun Ajaran</p>
                    <p class="font-semibold text-gray-700">{{ $rombel->tahunAjaran->nama ?? '-' }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-3 w-10">No</th>
                            <th class="px-4 py-3">Nama Siswa</th>
                            <th class="px-4 py-3">NIS / NISN</th>
                            <th class="px-4 py-3 text-center">Status SPP (Bulan Ini)</th>
                            <th class="px-4 py-3 text-center">Tunggakan SPP</th>
                            <th class="px-4 py-3 text-center">Kegiatan Tahunan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($students as $index => $student)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-center">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $student['nama'] }}</td>
                                <td class="px-4 py-3 text-gray-500">{{ $student['nis'] }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($student['spp_bulan_ini'])
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Lunas</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Belum</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($student['tunggakan_spp'] > 0)
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $student['tunggakan_spp'] }} Bulan</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($student['status_kegiatan'] == 'Lunas')
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Lunas</span>
                                    @elseif($student['status_kegiatan'] == 'Sebagian')
                                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Sebagian</span>
                                    @else
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Belum Lunas</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500 italic">
                                    Tidak ada data siswa pada rombel ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</x-filament-panels::page>
