<?php

namespace App\Services\SmartTwin;

use App\Enums\AdviceSource;
use App\Enums\SmartTwin\WoonplanState;
use App\Helpers\Models\BuildingSettingHelper;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\UserActionPlanAdvice;

/**
 * Locks the resident's woonplan while a coach works on it in SmartTwin.
 *
 * SmartTwin sends `smarttwin.advice.started` when a coach starts an advice; that locks the plan. The
 * coach releases it by setting the building's status to "Uitgevoerd". One building setting holds it,
 * false — or absent — meaning open.
 *
 * Only a limit on what the resident can do on the woonplan page. The coach may still change the
 * plan, and an import is always processed: a quickscan cannot be started while the plan is locked,
 * and should a result come in regardless, it is applied like any other.
 */
class WoonplanLock
{
    public function lock(Building $building): void
    {
        BuildingSettingHelper::syncSettings($building, [BuildingSettingHelper::SHORT_WOONPLAN_LOCKED => '1']);
    }

    public function release(Building $building): void
    {
        BuildingSettingHelper::syncSettings($building, [BuildingSettingHelper::SHORT_WOONPLAN_LOCKED => '0']);
    }

    public function isLocked(Building $building): bool
    {
        return filter_var(
            BuildingSettingHelper::getSettingValue($building, BuildingSettingHelper::SHORT_WOONPLAN_LOCKED, '0'),
            FILTER_VALIDATE_BOOLEAN,
        );
    }

    /**
     * Locked without a coach plan is "Nog even geduld"; locked with one shows that plan, read-only.
     *
     * A coach plan is any SmartTwin advice in the coach's input source. On a second round that is
     * the previous plan, which is then shown locked rather than hidden — deliberately: it is there.
     * Advices the calculation made for the coach before SmartTwin do not count, or the very first
     * start would skip the waiting message.
     */
    public function state(Building $building): WoonplanState
    {
        if (! $this->isLocked($building)) {
            return WoonplanState::OPEN;
        }

        $hasCoachPlan = UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $building->user_id)
            ->where('input_source_id', InputSource::findByShort(InputSource::COACH_SHORT)->id)
            ->where('user_action_plan_advisable_type', MeasureApplication::class)
            ->where('source', AdviceSource::SMART_TWIN)
            ->exists();

        return $hasCoachPlan ? WoonplanState::LOCKED : WoonplanState::AWAITING_COACH;
    }
}
