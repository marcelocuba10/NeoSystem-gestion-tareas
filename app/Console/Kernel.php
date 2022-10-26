<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        //\Modules\User\Console\ApiCron::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        //$schedule->command('api:cron')->everyMinute()->withoutOverlapping(1);
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
