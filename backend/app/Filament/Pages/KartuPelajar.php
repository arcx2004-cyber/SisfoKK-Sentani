<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class KartuPelajar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Kartu Pelajar';
    protected static ?int $navigationSort = 2;
    protected static ?string $title = 'Kartu Pelajar';
    protected static ?string $slug = 'kartu-pelajar';
    protected static string $view = 'filament.pages.kartu-pelajar';
    
    public $student;

    public function mount()
    {
        $this->student = Auth::user()->siswa;
        if (!$this->student) {
             // Optional: Notification::make()->title('Data Siswa tidak ditemukan')->danger()->send();
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Only show for 'siswa' role
        return Auth::user() && Auth::user()->hasRole('siswa');
    }
}
