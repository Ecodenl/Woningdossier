<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\SolarPanel;
use App\Events\StepCleared;
use App\Models\Building;
use App\Models\BuildingPvPanel;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Scopes\GetValueScope;
use App\Services\UserActionPlanAdviceService;

class SolarPanelHelper extends ToolHelper
{

    public function saveValues(): ToolHelper
    {
        BuildingPvPanel::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $this->building->id,
                'input_source_id' => $this->inputSource->id,
            ],
            $this->getValues('building_pv_panels')
        );

        $this
            ->user
            ->energyHabit()
            ->forInputSource($this->inputSource)
            ->update($this->getValues('user_energy_habits'));

        return $this;
    }

    public function createAdvices(): ToolHelper
    {
        $step = Step::findByShort('solar-panels');

        $results = SolarPanel::calculate($this->building, $this->getValues());

        // remove old results
        UserActionPlanAdviceService::clearForStep($this->user, $this->inputSource, $step);

        if (isset($results['cost_indication']) && $results['cost_indication'] > 0) {
            $measureApplication = MeasureApplication::where('short', 'solar-panels-place-replace')->first();
            if ($measureApplication instanceof MeasureApplication) {
                $actionPlanAdvice = new UserActionPlanAdvice($results);
                $actionPlanAdvice->costs = $results['cost_indication'];
                $actionPlanAdvice->input_source_id = $this->inputSource->id;
                $actionPlanAdvice->savings_electricity = $results['yield_electricity'];
                $actionPlanAdvice->user()->associate($this->user);
                $actionPlanAdvice->measureApplication()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($step);
                $actionPlanAdvice->save();
            }
        }
        return $this;
    }

    public function createValues(): ToolHelper
    {
        $buildingPvPanels = $this->building->pvPanels()->forInputSource($this->inputSource)->first();
        $userEnergyHabit = $this->user->energyHabit()->forInputSource($this->inputSource)->first();


        $userInterestsForSolarPanels = $this
            ->user
            ->userInterestsForSpecificType(Step::class, Step::findByShort('solar-panels')->id, $this->inputSource)
            ->first();

        $this->setValues([
            'building_pv_panels' => $buildingPvPanels instanceof BuildingPvPanel ? $buildingPvPanels->toArray() : [],
            'user_energy_habits' => [
                'amount_electricity' => $userEnergyHabit->amount_electricity ?? null,
            ],
            'user_interests' => [
                'interested_in_id' => optional($userInterestsForSolarPanels)->interested_in_id,
                'interested_in_type' => Step::class,
                'interest_id' => optional($userInterestsForSolarPanels)->interest_id,
            ],
        ]);
        return $this;
    }

    /**
     * Method to clear the pv panels
     *
     * @param Building $building
     * @param InputSource $inputSource
     */
    public static function clear(Building $building, InputSource $inputSource)
    {
        BuildingPvPanel::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            [
                'peak_power' => null,
                'number' => null,
                'pv_panel_orientation_id' => null,
                'angle' => null,
            ]
        );

        StepCleared::dispatch($building->user, $inputSource, Step::findByShort('solar-panels'));
    }
}