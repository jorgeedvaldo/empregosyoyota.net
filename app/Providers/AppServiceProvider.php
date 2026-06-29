<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;
use Filament\Facades\Filament;

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
