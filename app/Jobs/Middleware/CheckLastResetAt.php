<?php

namespace App\Jobs\Middleware;

use App\Jobs\ResetDossierForUser;
use App\Models\Building;
use App\Models\InputSource;
use App\Services\DossierSettingsService;
use Carbon\Carbon;
use Illuminate\Bus\Batch;
use Illuminate\Bus\Batchable;
use Illuminate\Queue\Jobs\DatabaseJob;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CheckLastResetAt
{
    public Building $building;

    public function __construct(Building $building)
    {
        $this->building = $building;
    }

    /**
     * Process the job.
     *
     * @param  mixed  $job
     * @param  callable  $next
     * @return mixed
     */
    public function handle($job, $next)
    {
        if ($job->connection !== "sync") {
            if ($this->isBatchedJob($job)) {
                if ($job->batch()->cancelled()) {
                    Log::debug('Batch has been cancelled!, skipping job.');
                    return;
                }
                $id = $job->batch()->id;
                $displayName = $job->batch()->name;
            } else {
                $id = $job->getJobUuid();
                $displayName = get_class($job->job);
            }

            Log::debug("{$displayName} [{$id}] Checking for reset cached time: ".Cache::get($id));
            $jobQueuedAt = Carbon::createFromFormat('Y-m-d H:i:s', Cache::get($id));

            $resetIsDoneAfterThisJobHasBeenQueued = app(DossierSettingsService::class)
                ->forBuilding($this->building)
                ->forInputSource(InputSource::master())
                ->forType(ResetDossierForUser::class)
                ->isDoneAfter($jobQueuedAt);


            $yesONo = $resetIsDoneAfterThisJobHasBeenQueued ? 'yes!' : 'no!';
            Log::debug("ResetDone after job queued: {$yesONo}");
            if ($resetIsDoneAfterThisJobHasBeenQueued) {
                // notify that the batch is cancelled.
                if ($this->isBatchedJob($job)) {
                    $job->batch()->cancel();
                }
                // cancel the execution of the job itself
                return;
            } else {
                $next($job);
            }
        }
    }

    private function isBatchedJob($job): bool
    {
        return in_array(Batchable::class, class_uses($job)) && $job->batch() instanceof Batch;
    }
}
