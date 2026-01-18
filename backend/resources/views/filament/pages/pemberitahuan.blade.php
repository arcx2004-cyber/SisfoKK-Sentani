<x-filament-panels::page>
    <div class="space-y-6">
        @forelse($announcements as $item)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 transition hover:shadow-md">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <span class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-blue-100 text-blue-600">
                                <x-heroicon-o-megaphone class="h-6 w-6" />
                            </span>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ $item->judul }}</h3>
                            <p class="text-xs text-gray-500">{{ $item->published_at ? $item->published_at->format('d M Y, H:i') : $item->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="prose max-w-none text-gray-600">
                    {!! $item->konten !!}
                </div>

                @if($item->featured_image)
                    <div class="mt-4">
                        <img src="{{ asset('storage/' . $item->featured_image) }}" class="rounded-lg max-h-64 object-cover" alt="Announcement Image">
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                <x-heroicon-o-bell-slash class="mx-auto h-12 w-12 text-gray-400" />
                <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada pemberitahuan</h3>
                <p class="mt-1 text-sm text-gray-500">Belum ada informasi terbaru dari sekolah.</p>
            </div>
        @endforelse
    </div>
</x-filament-panels::page>
