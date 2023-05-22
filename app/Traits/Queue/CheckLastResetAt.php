<?php

namespace App\Traits\Queue;

use App\Jobs\ResetDossierForUser;
use App\Models\Building;
use App\Models\InputSource;
use App\Services\DossierSettingsService;

trait CheckLastResetAt
{
    public function resetIsDoneAfterThisJobHasBeenQueued(Building $building, InputSource $inputSource, $queuedAt): bool
    {
        return app(DossierSettingsService::class)
            ->forInputSource($inputSource)
            ->forBuilding($building)
            ->forType(ResetDossierForUser::class)
            ->isDoneAfter($queuedAt);
    }
}