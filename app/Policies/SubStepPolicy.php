<?php

namespace App\Policies;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\HoomdossierSession;
use App\Helpers\QuickScanHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;

class SubStepPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function show(Account $account, SubStep $subStep, Building $building = null)
    {
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
