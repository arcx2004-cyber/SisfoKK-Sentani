<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Navigation\NavigationGroup;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->profile(\App\Filament\Pages\Auth\EditProfile::class)
            ->brandName('Kalam Kudus Sentani')
            ->brandLogo(asset('logo.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('logo.png'))
            ->colors([
                'primary' => Color::Blue,
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Data Master')
                    ->icon('heroicon-o-cog-6-tooth'),
                NavigationGroup::make()
                    ->label('Akademik')
                    ->icon('heroicon-o-academic-cap'),
                NavigationGroup::make()
                    ->label('Kesiswaan')
                    ->icon('heroicon-o-users'),
                NavigationGroup::make()
                    ->label('Keuangan')
                    ->icon('heroicon-o-banknotes'),
                NavigationGroup::make()
                    ->label('PPDB')
                    ->icon('heroicon-o-user-plus'),
                NavigationGroup::make()
                    ->label('CMS')
                    ->icon('heroicon-o-document-text'),
                NavigationGroup::make()
                    ->label('Pengaturan')
                    ->icon('heroicon-o-cog'),
                NavigationGroup::make()
                    ->label('Laporan Pembayaran')
                    ->icon('heroicon-o-document-chart-bar'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\CustomAccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->renderHook(
                'panels::head.end',
                fn (): string => '<style>
                    /* Topbar Styling */
                    .fi-topbar, .fi-sidebar-header {
                        background-color: #1e40af !important; /* Blue-800 */
                        border-bottom: none !important;
                    }
                    .fi-topbar nav {
                        background-color: transparent !important;
                    }
                    /* Icon Colors */
                    .fi-topbar .fi-icon-btn {
                        color: white !important;
                    }
                    
                    /* Sidebar Brand/Logo styling */
                    .fi-sidebar-header .fi-logo-img {
                        background-color: white;
                        padding: 4px;
                        border-radius: 8px;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    }
                    /* Fallback if class not found on img, target generic img in brand */
                    .fi-sidebar-header a img {
                        background-color: white;
                        padding: 4px;
                        border-radius: 6px;
                    }
                </style>'
            )
            ->renderHook(
                'panels::body.end',
                fn (): \Illuminate\Contracts\View\View => view('filament.hooks.footer')
            );
    }
}
