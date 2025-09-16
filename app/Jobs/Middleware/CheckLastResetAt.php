<?php

namespace App\Jobs\Middleware;

use App\Models\Building;
use App\Models\InputSource;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Support\Facades\Log;

class CheckLastResetAt
{
    use \App\Traits\Queue\CheckLastResetAt;

    public Building $building;

    public function __construct(Building $building)
    {
        $this->building = $building;
    }

    /**
     * Process the job.
     *
     * @param  mixed  $job
     */
    public function handle($job, callable $next): void
    {
        // no logic should be applied when dispatched on sync
        if ($job->connection === "sync") {
            $next($job);
        } else {
            if ($this->isBatchedJob($job)) {
                if ($job->batch()->cancelled()) {
                    Log::debug('Batch has been cancelled!, skipping job.');
                    return;
                }
                $displayName = $job->batch()->name;
            } else {
                $displayName = get_class($job->job);
            }

            Log::debug("{$displayName} Checking for reset queued time: " . $job->queuedAt()->format('Y-m-d H:i:s'));


            $resetIsDoneAfterThisJobHasBeenQueued = $this->resetIsDoneAfterThisJobHasBeenQueued(
                $this->building,
                InputSource::master(),
                $job->queuedAt()
            );

            $yesONo = $resetIsDoneAfterThisJobHasBeenQueued ? 'yes!' : 'no!';
            Log::debug("ResetDone after job queued: {$yesONo}");
            if ($resetIsDoneAfterThisJobHasBeenQueued) {
                // notify that the batch is cancelled.
                if ($this->isBatchedJob($job)) {
                    $job->batch()->cancel();
                }
            } else {
                $next($job);
            }
        }
        return;
    }

    private function isBatchedJob($job): bool
    {
        return in_array(Batchable::class, class_uses($job)) && $job->batch() instanceof Batch;
    }
}
