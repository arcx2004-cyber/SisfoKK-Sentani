<x-filament-panels::page>
    <form wire:submit="printAction">
        {{ $this->form }}
        
        <div class="mt-4 flex justify-end">
            <x-filament::button type="submit">
                Cetak Rapor (PDF)
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
