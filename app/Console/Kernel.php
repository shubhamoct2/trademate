<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use JoeDixon\Translation\Console\Commands\SynchroniseMissingTranslationKeys;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('profit:share')
            ->everyMinute()
            ->runInBackground(); 

        $schedule->command('save:history')
            ->dailyAt('00:00')
            ->runInBackground(); 

        $schedule->command('deposit:cancel')
            ->hourly()
            ->runInBackground(); 

        $schedule->command('auto:ranking')
            ->hourly()
            ->runInBackground(); 
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

    protected $commands = [
        SynchroniseMissingTranslationKeys::class,
    ];
}
