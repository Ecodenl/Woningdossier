<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\HighEfficiencyBoiler;
use App\Models\Building;
use App\Models\BuildingService;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Scopes\GetValueScope;
use App\Scopes\VisibleScope;
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
            ->forInputSource($this->inputSource)
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
                    'is_considering' => $this->user->considers($step, $this->inputSource),
                ],
            ],
            'building_services' => $buildingBoilerArray,
            'user_energy_habits' => [
                'amount_gas' => $userEnergyHabit->amount_gas ?? null,
                'resident_count' => $userEnergyHabit->resident_count ?? null,
            ],
        ]);

        return $this;
    }

    public function createAdvices(): ToolHelper
    {
        $userEnergyHabit = $this->user->energyHabit()->forInputSource($this->inputSource)->first();
        $results = HighEfficiencyBoiler::calculate($userEnergyHabit, $this->getValues());

        $step = Step::findByShort('high-efficiency-boiler');

        $oldAdvices = UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        if ($this->considers($step) && isset($results['cost_indication']) && $results['cost_indication'] > 0) {
            $measureApplication = MeasureApplication::where('short', 'high-efficiency-boiler-replace')->first();
            if ($measureApplication instanceof MeasureApplication) {
                $actionPlanAdvice = new UserActionPlanAdvice($results);
                $actionPlanAdvice->input_source_id = $this->inputSource->id;
                $actionPlanAdvice->costs = ['from' => $results['cost_indication']];
                $actionPlanAdvice->year = $results['replace_year'];
                $actionPlanAdvice->user()->associate($this->user);
                $actionPlanAdvice->userActionPlanAdvisable()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($step);

                UserActionPlanAdviceService::checkOldAdvices($actionPlanAdvice, $measureApplication, $oldAdvices);

                $actionPlanAdvice->save();
            }
        }

        return $this;
    }
}
