<?php

namespace App\Listeners;

use App\Contracts\Queue\ShouldNotHandleAfterBuildingReset;
use App\Models\InputSource;
use App\Services\DossierSettingsService;
use App\Traits\Queue\CheckLastResetAt;
use App\Traits\Queue\RegisterQueuedJobTime;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

abstract class NonHandleableListenerAfterReset implements ShouldQueue, ShouldNotHandleAfterBuildingReset
{
    use CheckLastResetAt, RegisterQueuedJobTime, InteractsWithQueue;

    public DossierSettingsService $dossierSettingsService;

    public function __construct(DossierSettingsService $dossierSettingsService)
    {
        $this->dossierSettingsService = $dossierSettingsService;
        $this->registerQueuedTime();
    }

    public function checkLastResetAt(\Closure $closure, $building)
    {
        if ($this->job->getConnectionName() !== "sync") {
            $payload = $this->job->payload();
            $displayName = $payload['displayName'];

            Log::debug("{$displayName} Checking for reset cached time: ".$this->queuedAt());

            if ($this->resetIsDoneAfterThisJobHasBeenQueued($building, InputSource::master(), $this->queuedAt())) {
                return;
            } else {
                return $closure();
            }
        }
        return $closure();
    }
}
