<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\OnixsatImportMensagensCommand::class,
        Commands\OnixsatImportVeiculosCommand::class,
        Commands\PurgeDataBaseCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('onixsat:import-mensagens')->everyMinute();
        $schedule->command('onixsat:import-veiculos')->everyTenMinutes();
        // Executa todos os dias as 03hs da madrugada
        $schedule->command('purge:database')->dailyAt('03:00');
    }
}
