<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\HighEfficiencyBoiler;
use App\Calculations\SolarPanel;
use App\Events\StepCleared;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingPvPanel;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserEnergyHabit;
use App\Scopes\GetValueScope;
use App\Services\UserActionPlanAdviceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SolarPanelHelper
{

    /**
     * Method to save the data from the solar panel step
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param array $buildingFeatureData
     * @param array $buildingElementData
     */
    public static function save(Building $building, InputSource $inputSource, array $saveData)
    {
        BuildingPvPanel::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ],
            $saveData['building_pv_panels']
        );

        $building->user->energyHabit()->withoutGlobalScope(GetValueScope::class)->update($saveData['user_energy_habits']);

        self::saveAdvices($building, $inputSource, $saveData);
    }

    /**
     * Save the advices for the solar panels
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param array $saveData
     * @throws \Exception
     */
    public static function saveAdvices(Building $building, InputSource $inputSource, array $saveData)
    {
        $user = $building->user;
        $step = Step::findByShort('solar-panels');

        $results = SolarPanel::calculate($building, $saveData);

        // remove old results
        UserActionPlanAdviceService::clearForStep($user, $inputSource, $step);

        if (isset($results['cost_indication']) && $results['cost_indication'] > 0) {
            $measureApplication = MeasureApplication::where('short', 'solar-panels-place-replace')->first();
            if ($measureApplication instanceof MeasureApplication) {
                $actionPlanAdvice = new UserActionPlanAdvice($results);
                $actionPlanAdvice->costs = $results['cost_indication'];
                $actionPlanAdvice->savings_electricity = $results['yield_electricity'];
                $actionPlanAdvice->user()->associate($user);
                $actionPlanAdvice->measureApplication()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($step);
                $actionPlanAdvice->save();
            }
        }
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