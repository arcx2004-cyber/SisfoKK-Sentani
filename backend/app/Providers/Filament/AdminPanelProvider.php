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
use Filament\Navigation\MenuItem;
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
            ->userMenuItems([
                MenuItem::make()
                    ->label('Pesan Masuk')
                    ->url(fn (): string => \App\Filament\Resources\MessageResource::getUrl())
                    ->icon('heroicon-o-inbox'),
            ])
            ->brandName('Kalam Kudus Sentani')
            ->brandLogo(asset('logo.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('logo.png'))
            ->colors([
                'primary' => '#4caf50',
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'success' => Color::Emerald,
                'warning' => Color::Orange,
            ])
            ->font('Inter')
            ->databaseNotifications()
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
                fn (): string => "
                <link rel=\"stylesheet\" href=\"/css/custom-filament.css?v=1.1\">
                <style>
                    /* Custom Brand Styling for Dark Sidebar */
                    .fi-sidebar-header .fi-logo-img, .fi-sidebar-header a img {
                        background-color: white;
                        padding: 4px;
                        border-radius: 8px;
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                    }
                </style>"
            )
            ->renderHook(
                'panels::body.end',
                fn (): \Illuminate\Contracts\View\View => view('filament.hooks.footer')
            );
    }
}