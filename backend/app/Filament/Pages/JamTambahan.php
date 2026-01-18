<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\RombelEkskul;
use App\Models\Ekstrakurikuler;
use App\Models\Kokurikuler;

class JamTambahan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Jam Tambahan';
    protected static ?string $title = 'Kegiatan Tambahan (Ekskul & Kokurikuler)';
    protected static string $view = 'filament.pages.jam-tambahan';

    public $ekskuls = [];
    public $kokurikulers = []; // Assuming logic exists for Kokurikuler

    public function mount()
    {
        $student = Auth::user()->siswa;
        if (!$student) return;

        // 1. Get Ekskul
        $this->ekskuls = RombelEkskul::where('siswa_id', $student->id)
            ->with(['ekstrakurikuler'])
            ->get();

        // 2. Get Kokurikuler
        // Assuming AnggotaKokurikuler links Siswa and Kokurikuler
        $this->kokurikulers = \App\Models\AnggotaKokurikuler::where('siswa_id', $student->id)
            ->with(['kokurikuler', 'pembimbing']) 
            ->get();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user() && Auth::user()->hasRole('siswa');
    }
}
