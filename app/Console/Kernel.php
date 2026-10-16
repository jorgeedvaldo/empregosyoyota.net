<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // De proposito, sem nada aqui: a tolerancia a falhas do MySQL
        // (config/resilience.php) nao depende de Cron Job nenhum. O
        // snapshot de emergencia (App\Services\EmergencySnapshotService)
        // actualiza-se sozinho, a cada vaga/artigo/categoria gravado ou
        // apagado — ver docs/resiliencia-mysql.md.
        //
        // "php artisan resilience:snapshot:rebuild" existe para gerar a
        // primeira copia valida depois de publicar isto, ou para uma
        // reconciliacao manual; nunca e chamado automaticamente.
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
