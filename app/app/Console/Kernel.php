<?php

namespace App\Console;

use App\Jobs\ScheduledTranfer;
use App\Jobs\TaskApiRunner;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('app:api-command')->everyThirtySeconds();
        //$schedule->job(new ScheduledTranfer, 'scheduled_transfers')->everyMinute();
        //$schedule->job(new ScheduledTranfer, 'scheduled_transfers')->dailyAt('05:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
