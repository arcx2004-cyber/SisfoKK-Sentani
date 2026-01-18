<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\News; 

class Pemberitahuan extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'Pemberitahuan';
    protected static ?int $navigationSort = 3;
    protected static ?string $title = 'Pemberitahuan Sekolah';
    protected static string $view = 'filament.pages.pemberitahuan';

    public $announcements = [];

    public function mount()
    {
        // Fetch latest news/announcements
        // Assuming 'is_active' or similar exists.
        // Also assuming all news is visible to students for now.
        $this->announcements = News::latest()->take(10)->get(); 
    }

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::user() && Auth::user()->hasRole('siswa');
    }
}
