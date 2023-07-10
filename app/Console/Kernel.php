<?php

namespace App\Console;

use App\Console\Commands\Api\Econobis\Out\Hoomdossier\Gebruik;
use App\Console\Commands\Api\Econobis\Out\Hoomdossier\PdfReport;
use App\Console\Commands\Api\Econobis\Out\Hoomdossier\Woonplan;
use App\Console\Commands\Api\Verbeterjehuis\Mappings\SyncMeasures;
use App\Console\Commands\Api\Verbeterjehuis\Mappings\SyncTargetGroups;
use App\Console\Commands\Monitoring\MonitorQueue;
use App\Console\Commands\CleanupExpiredFileStorages;
use App\Console\Commands\SendNotifications;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        SendNotifications::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('send:notifications --type=private-message')->everyFifteenMinutes();

        $schedule->command(MonitorQueue::class)->everyFiveMinutes();

        $schedule->command('avg:cleanup-audits')->daily();
        $schedule->command(CleanupExpiredFileStorages::class)->hourly();

        $schedule->command(SyncTargetGroups::class)->daily();
        $schedule->command(SyncMeasures::class)->daily();

        $schedule->command(Gebruik::class)->daily();
        if (\App::environment() == 'accept') {
            $schedule->command(Woonplan::class)->everyMinute()->withoutOverlapping();
            $schedule->command(PdfReport::class)->everyMinute()->withoutOverlapping();
        } else {
            $schedule->command(Woonplan::class)->everyFiveMinutes()->withoutOverlapping();
            $schedule->command(PdfReport::class)->everyFiveMinutes()->withoutOverlapping();
        }


        // $schedule->command('inspire')
        //          ->hourly();
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
