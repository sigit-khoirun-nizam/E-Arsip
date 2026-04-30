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
use App\Filament\Widgets\DashboardHeader;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\QuickActions;
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
            ->brandName('E-Arsip')
            ->brandLogo(fn() => view('filament.logo'))
            ->brandLogoHeight('2.5rem')
            ->login(\App\Filament\Pages\Auth\CustomLogin::class)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->navigationGroups([
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Manajemen Arsip'),
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Korin'),
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Surat menyurat'),
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Manajemen Sistem'),
                \Filament\Navigation\NavigationGroup::make()
                     ->label('Filament Shield'),
            ])
            ->widgets([
                DashboardHeader::class,
                StatsOverview::class,
                QuickActions::class,
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
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make(),
                \Hasnayeen\Themes\ThemesPlugin::make()
                    ->registerTheme([
                        \App\Filament\Admin\Themes\Light::class,
                        \App\Filament\Admin\Themes\Dark::class,
                    ]),
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
