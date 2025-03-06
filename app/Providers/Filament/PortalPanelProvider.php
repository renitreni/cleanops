<?php

namespace App\Providers\Filament;

use App\Livewire\AdminWidgets;
use App\Services\AlertDetails;
use Filament\Enums\ThemeMode;
use Filament\Facades\Filament;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class PortalPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('portal')
            ->path('portal')
            ->login()
            ->defaultThemeMode(ThemeMode::Light)
            ->colors([
                'primary' => Color::Green,
            ])
            ->discoverResources(in: app_path('Filament/Portal/Resources'), for: 'App\\Filament\\Portal\\Resources')
            ->discoverPages(in: app_path('Filament/Portal/Pages'), for: 'App\\Filament\\Portal\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Portal/Widgets'), for: 'App\\Filament\\Portal\\Widgets')
            ->widgets([
                AdminWidgets::class,
            ])
            ->topNavigation()
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
            ->authMiddleware([
                Authenticate::class,
            ])
            ->viteTheme('resources/css/filament/portal/theme.css');
    }

    public function boot()
    {
        // Inject view at the start of the body in every Filament page
        Filament::registerRenderHook(
            'panels::content.start',
            function () {
                return view('alerts.pending', ['count' => app(AlertDetails::class)->pending()]);
            }
        );
        Filament::registerRenderHook(
            'panels::content.start',
            function () {
                return view('alerts.due', ['count' => app(AlertDetails::class)->due()]);
            }
        );
        Filament::registerRenderHook(
            'panels::content.start',
            fn () => view('alerts.in-progress', ['count' => app(AlertDetails::class)->inProgress()])
        );
        Filament::registerRenderHook(
            'panels::content.start',
            fn () => view('alerts.resolved')
        );
        Filament::registerRenderHook(
            'panels::content.start',
            fn () => view('alerts.emergency')
        );
    }
}
