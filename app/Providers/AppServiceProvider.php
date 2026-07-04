<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Pagination\Paginator;
use Filament\Facades\Filament;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Paginator::useBootstrap();

        // Estado global dos anuncios AdSense (controlado no painel de administracao).
        // Fallback seguro (ligado) caso a tabela ainda nao exista (ex.: antes de migrar).
        try {
            $adsEnabled = Setting::get('ads_enabled', '1') === '1';
        } catch (\Throwable $e) {
            $adsEnabled = true;
        }
        View::share('adsEnabled', $adsEnabled);

        // Painel Filament: troca a cor primaria (amber) por preto.
        // A cor "danger" (vermelho) mantem-se, logo os botoes de delete
        // continuam vermelhos.
        Filament::serving(function () {
            Filament::registerRenderHook(
                'styles.end',
                fn (): string => view('filament.theme-overrides')->render(),
            );
        });
    }
}
