<?php

namespace App\Policies;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\HoomdossierSession;
use App\Models\Account;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\SubStep;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class SubStepPolicy
{
    use HandlesAuthorization;

    public function show(Account $account, SubStep $subStep, Building $building = null): bool
    {
        // We don't want session data in the policy. This is here to ensure we didn't forget anything.
        // TODO: Remove this once alerts no longer trigger
        if (! $building instanceof Building) {
            Log::alert(__METHOD__ . " building is not set for URL " . request()->fullUrl());
            $building = HoomdossierSession::getBuilding(true);
        }

        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        return ConditionEvaluator::init()
            ->building($building)
            ->inputSource($masterInputSource)
            ->evaluate($subStep->conditions ?? []);
    }
}
