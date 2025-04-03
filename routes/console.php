<?php

use App\Console\Commands\SendNotifications;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\App;
use App\Console\Commands\CleanupExpiredFileStorages;
use App\Console\Commands\Monitoring\MonitorQueue;
use App\Console\Commands\AVG\CleanupPasswordResets;
use App\Console\Commands\AVG\CleanupAudits;
use App\Console\Commands\Api\Verbeterjehuis\Mappings\SyncTargetGroups;
use App\Console\Commands\Api\Verbeterjehuis\Mappings\SyncMeasures;
use App\Console\Commands\Api\Econobis\Out\Hoomdossier\Woonplan;
use App\Console\Commands\Api\Econobis\Out\Hoomdossier\PdfReport;
use App\Console\Commands\Api\Econobis\Out\Hoomdossier\Gebruik;

Schedule::command(SendNotifications::class, ['--type' => 'private-message'])->everyFifteenMinutes();

Schedule::command(MonitorQueue::class)->everyFiveMinutes();

Schedule::command(CleanupAudits::class)->daily();
Schedule::command(CleanupPasswordResets::class)->daily();
Schedule::command(CleanupExpiredFileStorages::class)->everyThirtyMinutes();

Schedule::command(SyncTargetGroups::class)->daily();
Schedule::command(SyncMeasures::class)->daily();

Schedule::command(Gebruik::class)->dailyAt('01:00');
if (App::environment() == 'accept') {
    Schedule::command(Woonplan::class)->everyMinute()->withoutOverlapping();
    Schedule::command(PdfReport::class)->everyMinute()->withoutOverlapping();
} else {
    Schedule::command(Woonplan::class)->everyFiveMinutes()->withoutOverlapping();
    Schedule::command(PdfReport::class)->everyFiveMinutes()->withoutOverlapping();
}
