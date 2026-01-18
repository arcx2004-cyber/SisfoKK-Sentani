<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Pages\Page;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';

    // Override layout if needed, but usually we can just style the view to take over if we use a raw layout.
    // However, Filament forces a specific auth layout. 
    // To get full control (split screen), we might need to override the layout used.
    // But let's try to just use the view and styling first. 
    // Actually, Filament's auth layout is typically 'filament-panels::components.layout.simple'.
    // We want a full screen layout.
    
    public function getLayout(): string
    {
        return 'filament.pages.auth.login-layout';
    }
}
