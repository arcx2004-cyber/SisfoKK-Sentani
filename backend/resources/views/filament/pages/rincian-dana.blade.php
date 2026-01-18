<x-filament-panels::page>
    {{-- Stats are rendered via getHeaderWidgets --}}
    <div class="mt-4">
        <div class="p-6 bg-white dark:bg-gray-800 rounded-lg shadow">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Keterangan</h3>
            <p class="text-gray-600 dark:text-gray-400">
                Halaman ini menampilkan ringkasan dana sekolah berdasarkan unit Anda. Data diambil dari:
            </p>
            <ul class="list-disc ml-5 mt-2 text-gray-600 dark:text-gray-400">
                <li><strong>Total Anggaran (RAPBS):</strong> Total pendapatan yang telah disetujui dalam RAPBS tahun ajaran aktif.</li>
                <li><strong>Pemasukan SPP:</strong> Total pembayaran SPP siswa yang berstatus "Lunas".</li>
                <li><strong>Pemasukan Kegiatan:</strong> Total pembayaran uang kegiatan siswa yang berstatus "Lunas".</li>
            </ul>
        </div>
    </div>
</x-filament-panels::page>
