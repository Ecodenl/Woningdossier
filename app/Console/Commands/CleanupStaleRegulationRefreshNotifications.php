<?php

namespace App\Console\Commands;

use App\Jobs\RefreshRegulationsForUserActionPlanAdvice;
use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CleanupStaleRegulationRefreshNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:cleanup-stale-regulation-refreshes {--hours=2 : Age in hours after which a notification is considered stale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete regulation refresh notifications whose job is long gone, so "my regulations" stops showing it is refreshing.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // A refresh is a handful of Verbeterjehuis requests. A notification that is this old belongs to a job that
        // has ended without clearing it, and would otherwise keep the refresh button disabled forever.
        $deleted = Notification::allInputSources()
            ->forType(RefreshRegulationsForUserActionPlanAdvice::class)
            ->where('created_at', '<', Carbon::now()->subHours((int) $this->option('hours')))
            ->delete();

        $this->info("Deleted {$deleted} stale regulation refresh notifications.");

        return self::SUCCESS;
    }
}
