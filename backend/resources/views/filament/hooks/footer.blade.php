@php
    $tahunAjaran = \App\Models\TahunAjaran::where('is_active', true)->first();
    $semester = \App\Models\Semester::where('is_active', true)->first();
    $ta = $tahunAjaran ? $tahunAjaran->nama : '-';
    $sem = $semester ? ucfirst($semester->tipe) : '-';
@endphp


<footer style="position: fixed; bottom: 0; width: 100%; background-color: #1e40af; color: white; padding: 0.5rem 1.5rem; text-align: center; font-size: 0.875rem; font-weight: 500; box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1); z-index: 9999;" class="print:hidden hidden md:block">
    <div style="display: flex; justify-content: space-between; align-items: center; max-width: 80rem; margin: 0 auto;">
        <div>
            Tahun Ajaran {{ $ta }} | Semester {{ $sem }}
        </div>
        <div>
            &copy; {{ date('Y') }} Agus Sutikno - Website SKKK Sentani
        </div>
    </div>
</footer>

{{-- Mobile Footer (Stacked) --}}
<footer style="position: fixed; bottom: 0; width: 100%; background-color: #1e40af; color: white; padding: 0.5rem 1rem; text-align: center; font-size: 0.75rem; box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1); z-index: 9999;" class="print:hidden md:hidden">
    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
        <div>TA {{ $ta }} | {{ $sem }}</div>
        <div>&copy; Agus Sutikno - Website SKKK Sentani</div>
    </div>
</footer>

{{-- Spacer to prevent content from being hidden behind footer --}}
<div class="h-12 w-full print:hidden"></div>
