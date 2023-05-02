<?php

namespace App\Jobs\Middleware;

use App\Jobs\ResetDossierForUser;
use App\Models\Building;
use App\Models\InputSource;
use App\Services\DossierSettingsService;
use Carbon\Carbon;
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
            $id = $job->job->payload()['id'];
            $displayName = get_class($job->job);
            Log::debug("{$displayName} [{$id}] Checking for reset cached time: ".Cache::get($id));
            $jobQueuedAt = Carbon::createFromFormat('Y-m-d H:i:s', Cache::get($id));

            $resetIsDoneAfterThisJobHasBeenQueued = app(DossierSettingsService::class)
                ->forType(ResetDossierForUser::class)
                ->forBuilding($this->building)
                ->forInputSource(InputSource::master())
                ->lastDoneAfter($jobQueuedAt);


            $yesONo = $resetIsDoneAfterThisJobHasBeenQueued ? 'yes!' : 'no!';
            Log::debug("ResetDone after job queued: {$yesONo}");
            if ($resetIsDoneAfterThisJobHasBeenQueued) {
                return;
            } else {
                $next($job);
            }
        }
    }
}
