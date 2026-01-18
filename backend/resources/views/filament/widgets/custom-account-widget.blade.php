<x-filament-widgets::widget>
    <x-filament::section class="py-2">
        <div class="flex items-center gap-x-6">
            <div class="shrink-0 relative">
                <img 
                    src="{{ $this->getUser()->getFilamentAvatarUrl() }}" 
                    alt="{{ $this->getUser()->name }}" 
                    class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-md bg-gray-200"
                >
            </div>
            
            <div class="min-w-0">
                <h2 class="text-3xl font-bold tracking-tight text-gray-950 dark:text-white sm:text-4xl">
                    Selamat Datang
                </h2>
                <p class="mt-1 text-lg font-medium text-gray-500 dark:text-gray-400 truncate">
                    {{ $this->getUser()->name }}
                </p>
            </div>

            <div class="flex-1"></div>

            @php
                $user = $this->getUser();
                $siswa = $user->siswa;
            @endphp
            @if ($user->hasRole('siswa') && $siswa)
                 @php
                    $unit = $siswa->unit ? $siswa->unit->nama : '-';
                    $rombel = $siswa->getCurrentRombel();
                    $activeTa = \App\Models\TahunAjaran::where('is_active', true)->first();
                    $activeSem = \App\Models\Semester::where('is_active', true)->first();
                 @endphp
                <div class="hidden lg:block mr-8">
                    <div class="flex flex-col gap-y-2 text-lg">
                        <div class="flex items-center">
                            <span class="w-32 shrink-0 font-medium text-gray-500 dark:text-gray-400">Status</span>
                            <span class="font-bold text-gray-900 dark:text-white">: Pelajar {{ $unit }}</span>
                        </div>
                        
                        <div class="flex items-center">
                            <span class="w-32 shrink-0 font-medium text-gray-500 dark:text-gray-400">Kelas</span>
                            <span class="font-bold text-gray-900 dark:text-white">: {{ $rombel->nama ?? '-' }}</span>
                        </div>

                        <div class="flex items-center">
                             <span class="w-32 shrink-0 font-medium text-gray-500 dark:text-gray-400">Tahun Ajaran</span>
                             <span class="font-bold text-gray-900 dark:text-white">: {{ $activeTa->nama ?? '-' }}</span>
                        </div>

                        <div class="flex items-center">
                             <span class="w-32 shrink-0 font-medium text-gray-500 dark:text-gray-400">Semester</span>
                             <span class="font-bold text-gray-900 dark:text-white">: {{ $activeSem ? ucwords($activeSem->tipe) : '-' }}</span>
                        </div>
                    </div>
                </div>
            @endif
            


            <div class="flex items-center gap-x-4">
                <form action="{{ filament()->getLogoutUrl() }}" method="post">
                    @csrf
                    <x-filament::button 
                        color="gray" 
                        icon="heroicon-m-arrow-right-on-rectangle" 
                        type="submit" 
                        outlined
                        class="hidden sm:inline-flex"
                    >
                        Keluar
                    </x-filament::button>
                    <!-- Mobile Icon Only -->
                    <x-filament::icon-button
                        color="gray"
                        icon="heroicon-m-arrow-right-on-rectangle"
                        type="submit"
                        class="sm:hidden"
                        label="Keluar"
                    />
                </form>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
