<?php

namespace App\Traits\Queue;

use App\Jobs\ResetDossierForUser;
use App\Models\InputSource;
use App\Services\DossierSettingsService;
use Carbon\Carbon;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Jobs\DatabaseJob;
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
        if ($this->job->getConnectionName() !== "sync") {
            $payload = $this->job->payload();
            $id = $this->job->getJobId();
            $displayName = $payload['displayName'];

            Log::debug("{$displayName} [{$id}] Checking for reset cached time: ".Cache::get($id));
            $jobQueuedAt = Carbon::createFromFormat('Y-m-d H:i:s', Cache::get($id));

            $resetIsDoneAfterThisJobHasBeenQueued = $this
                ->dossierSettingsService
                ->forInputSource(InputSource::master())
                ->forBuilding($building)
                ->forType(ResetDossierForUser::class)
                ->isDoneAfter($jobQueuedAt);

            if ($resetIsDoneAfterThisJobHasBeenQueued) {
                return;
            } else {
                return $closure();
            }
        }
    }

}