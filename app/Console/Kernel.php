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
        // Mantem as copias de seguranca frescas enquanto o MySQL esta bom:
        // o que nao estiver guardado antes da falha nao ha como servir
        // durante a falha.
        //
        // O withoutOverlapping() importa mais aqui do que o habitual: uma
        // execucao que demore mais do que o intervalo nao pode acumular
        // copias a correr ao mesmo tempo — cada uma ocupa ligacoes das 30
        // que o alojamento permite. O bloqueio vive na cache de ficheiros,
        // nao no MySQL, por isso funciona mesmo durante uma falha.
        $schedule->command('resilience:warm --pages')
            ->everyThirtyMinutes()
            ->withoutOverlapping(10);

        // Apaga copias expiradas e mantem a pasta dentro do limite, para
        // nao ir comendo a quota de disco da conta.
        $schedule->command('resilience:prune')
            ->dailyAt('04:10')
            ->withoutOverlapping(30);
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
