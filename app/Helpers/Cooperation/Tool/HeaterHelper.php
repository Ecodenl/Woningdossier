<?php

namespace App\Helpers\Cooperation\Tool;

use App\Calculations\Heater;
use App\Calculations\SolarPanel;
use App\Events\StepCleared;
use App\Helpers\HoomdossierSession;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeater;
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

class HeaterHelper
{

    /**
     * Method to clear all the saved data for the step, except for the comments.
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param array $buildingFeatureData
     * @param array $buildingElementData
     */
    public static function save(Building $building, InputSource $inputSource, array $saveData)
    {
        BuildingHeater::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id
            ],
            $saveData['building_heaters']
        );

        $building
            ->user
            ->energyHabit()
            ->withoutGlobalScope(GetValueScope::class)
            ->update($saveData['user_energy_habits']);

        self::saveAdvices($building, $inputSource, $saveData);
    }

    public static function saveAdvices(Building $building, InputSource $inputSource, array $saveData)
    {
        $user = $building->user;
        $step = Step::findByShort('heater');

        $results = Heater::calculate($building, $user->energyHabit, $saveData);

        // remove old results
        UserActionPlanAdviceService::clearForStep($user, $inputSource, $step);

        if (isset($results['cost_indication']) && $results['cost_indication'] > 0) {
            $measureApplication = MeasureApplication::where('short', 'heater-place-replace')->first();
            if ($measureApplication instanceof MeasureApplication) {
                $actionPlanAdvice = new UserActionPlanAdvice($results);
                $actionPlanAdvice->costs = $results['cost_indication']; // only outlier
                $actionPlanAdvice->user()->associate($user);
                $actionPlanAdvice->measureApplication()->associate($measureApplication);
                $actionPlanAdvice->step()->associate($step);
                $actionPlanAdvice->save();
            }
        }
    }

    /**
     * Method to clear the building feature data for wall insulation step.
     *
     * @param Building $building
     * @param InputSource $inputSource
     */
    public static function clear(Building $building, InputSource $inputSource)
    {
        $building->heater()->forInputSource($inputSource)->delete();

        $building
            ->user
            ->energyHabit()
            ->withoutGlobalScope(GetValueScope::class)
            ->update([
                'water_comfort_id' => null,
            ]);

        StepCleared::dispatch($building->user, $inputSource, Step::findByShort('heater'));
    }
}