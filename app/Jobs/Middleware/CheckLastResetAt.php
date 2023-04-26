<?php

namespace App\Jobs\Middleware;

use App\Models\Building;
use App\Services\DossierSettingsService;
use Carbon\Carbon;
use Illuminate\Queue\Jobs\SyncJob;
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
            Log::debug("Checking for reset payloadId: ".$job->job->payload()['displayName']." [{$id}] cached time: ".Cache::get($id));

    //        dd($x->getDatabaseJob());
    //        $dossierSettingService = app(DossierSettingsService::class)
    //            ->forBuilding($this->building)
    //            ->lastDoneBefore();
            $next($job);
        }

        Log::debug('Job done');
    }
}
