<?php

namespace App\Traits\Queue;

use App\Services\DossierSettingsService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

trait CheckLastResetAt
{
    use InteractsWithQueue;

    public DossierSettingsService $dossierSettingsService;

    public function __construct(DossierSettingsService $dossierSettingsService)
    {
        $this->dossierSettingsService = $dossierSettingsService;
    }

    public function checkLastResetAt(\Closure $closure, $building)
    {
        if (!empty($this->job->payload()))
        if ($this->job->connection !== "sync") {
            $id = $this->job->job->payload()['id'];
            Log::debug("Checking for reset payloadId: ".$job->job->payload()['displayName']." [{$id}] cached time: ".Cache::get($id));

            $this->dossierSettingsService
                ->forInputSource()
                ->forBuilding($building)
                ->lastDoneBefore();
            $next($job);
            $closure();
        }
    }

}