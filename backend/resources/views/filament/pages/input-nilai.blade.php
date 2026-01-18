<x-filament-panels::page>
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}
   
        @if(!empty($students) && !empty($tps))
            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-950/5 dark:ring-white/10 rounded-xl overflow-hidden mt-6">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50/50 dark:bg-gray-800/50 dark:text-gray-200 border-b border-gray-200 dark:border-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold tracking-wide">Nama Siswa</th>
                                @foreach($tps as $tp)
                                    <th scope="col" class="px-6 py-4 text-center min-w-[120px]" title="{{ $tp->deskripsi }}">
                                        <div class="flex flex-col items-center gap-1">
                                            <span class="font-bold text-primary-600 dark:text-primary-400">{{ $tp->kode }}</span>
                                            {{-- <span class="text-[10px] text-gray-400 font-normal normal-case line-clamp-1 max-w-[150px]">{{ $tp->deskripsi }}</span> --}}
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($students as $siswa)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition duration-75">
                                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap bg-white/50 dark:bg-gray-900/50">
                                        {{ $siswa->nama_lengkap }}
                                    </td>
                                    @foreach($tps as $tp)
                                        <td class="px-6 py-3 text-center">
                                                type="number" 
                                                step="0.01" 
                                                max="100"
                                                wire:model="scores.{{ $siswa->id }}.{{ $tp->id }}"
                                                class="w-24 text-center text-sm font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded-lg shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 transition-all placeholder-gray-300"
                                                placeholder="0"
                                            >
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <x-filament::button type="submit">
                    Simpan Nilai
                </x-filament::button>
            </div>
        @elseif($rombel_id && $mata_pelajaran_id && $students->isEmpty())
             <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300" role="alert">
                <span class="font-medium">Warning!</span> Tidak ada siswa di rombel ini.
            </div>
        @elseif($rombel_id && $mata_pelajaran_id && $tps->isEmpty())
             <div class="p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-300" role="alert">
                <span class="font-medium">Warning!</span> Tidak ada Tujuan Pembelajaran (TP) untuk Mapel ini di semester ini. Pastikan CP dan TP sudah dibuat.
            </div>
        @endif
    </x-filament-panels::form>
</x-filament-panels::page>
