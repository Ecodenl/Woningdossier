<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\HighEfficiencyBoiler;
use App\Models\BuildingService;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Scopes\GetValueScope;
use App\Services\ConditionService;
use App\Services\UserActionPlanAdviceService;

class HighEfficiencyBoilerHelper extends ToolHelper
{
    public function saveValues(): ToolHelper
    {
        $service = Service::findByShort('boiler');

        BuildingService::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
                'service_id' => $service->id,
            ],
            $this->getValues('building_services')
        );

        UserEnergyHabit::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'user_id' => $this->user->id,
                'input_source_id' => $this->inputSource->id,
            ],
            $this->getValues('user_energy_habits')
        );

        return $this;
    }

    public function createValues(): ToolHelper
    {
        $boilerService = Service::findByShort('boiler');
        $step = Step::findByShort('high-efficiency-boiler');
        $userEnergyHabit = $this
            ->user
            ->energyHabit()
            ->forInputSource($this->inputSource)
            ->first();

        $buildingBoilerService = $this
            ->building
            ->buildingServices()
            ->where('service_id', $boilerService->id)
            ->forInputSource($this->masterInputSource)
            ->first();

        $buildingBoilerArray = [
            'service_value_id' => $buildingBoilerService->service_value_id ?? null,
            'extra' => [
                'date' => $buildingBoilerService->extra['date'] ?? null,
            ],
        ];

        $this->setValues([
            'considerables' => [
                $step->id => [
                    'is_considering' => $this->considersByConditions(
                        $this->getConditionConsiderable('hr-boiler')
                    ),
                ],
            ],
            //'has_completed_expert' => ConditionService::init()->building($this->building)->inputSource($this->inputSource)->hasCompletedSteps(['heating']),
            'building_services' => $buildingBoilerArray,
            'user_energy_habits' => [
                'amount_gas' => $userEnergyHabit->amount_gas ?? null,
                'resident_count' => $userEnergyHabit->resident_count ?? null,
            ],
            'updated_measure_ids' => [],
        ]);

        return $this;
    }

    public function createAdvices(): ToolHelper
    {
        $updatedMeasureIds = $this->getValues('updated_measure_ids');

        $results = HighEfficiencyBoiler::calculate($this->building, $this->inputSource);

        $step = Step::findByShort('high-efficiency-boiler');

        $oldAdvices = UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        // make sure the user considers the step
        // and has a cost indication before creating a advice
        if ($this->considers($step) && isset($results['cost_indication']) && $results['cost_indication'] > 0) {
            $measureApplication = MeasureApplication::where('short', 'high-efficiency-boiler-replace')->first();
            if ($measureApplication instanceof MeasureApplication) {
                $actionPlanAdvice = new UserActionPlanAdvice($results);
                $actionPlanAdvice->input_source_id = $this->inputSource->id;
                $actionPlanAdvice->costs = UserActionPlanAdviceService::formatCosts($results['cost_indication']);
                $actionPlanAdvice->year = $results['replace_year'];
                $actionPlanAdvice->user()->associate($this->user);
                $actionPlanAdvice->userActionPlanAdvisable()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($step);

                // We only want to check old advices if the updated attributes are not relevant to this measure
                if (! in_array($measureApplication->id, $updatedMeasureIds) && $this->shouldCheckOldAdvices()) {
                    UserActionPlanAdviceService::checkOldAdvices($actionPlanAdvice, $measureApplication, $oldAdvices);
                }

                $actionPlanAdvice->save();
            }
        }

        return $this;
    }
}
