<?php

namespace App\Jobs\Middleware;

use App\Models\Building;
use App\Services\DossierSettingsService;
use Carbon\Carbon;
use Illuminate\Queue\Jobs\SyncJob;
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
        /** @var SyncJob $x */
        $x = $job;
//        dd($x->getDatabaseJob());
        Log::debug('Pre job');
//        $dossierSettingService = app(DossierSettingsService::class)
//            ->forBuilding($this->building)
//            ->lastDoneBefore();
        $next($job);

        Log::debug('Job done');
    }
}
