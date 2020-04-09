<?php

namespace App\Helpers\Cooperation\Tool;

use App\Events\StepCleared;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingService;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\Service;
use App\Models\Step;
use App\Models\UserEnergyHabit;
use App\Scopes\GetValueScope;

class HighEfficiencyBoilerHelper
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
        $service = Service::findByShort('boiler');
        BuildingService::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'service_id' => $service->id,
            ],
            $saveData['building_services']
        );

        UserEnergyHabit::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'user_id' => $building->user->id,
                'input_source_id' => $inputSource->id,
            ],
            $saveData['user_energy_habits']
        );
    }

    /**
     * Method to clear the hr step
     *
     * @param Building $building
     * @param InputSource $inputSource
     */
    public static function clear(Building $building, InputSource $inputSource)
    {
        $service = Service::findByShort('boiler');

        BuildingService::forMe($building->user)
            ->forInputSource($inputSource)
            ->where('service_id', $service->id)
            ->delete();


        // questionable reset as this is base data
        UserEnergyHabit::withoutGlobalScope(GetValueScope::class)->updateOrCreate(
            [
                'user_id' => $building->user->id,
                'input_source_id' => $inputSource->id,
            ],
            [
                'amount_gas' => null,
                'resident_count' => null,
            ]
        );

        StepCleared::dispatch($building->user, $inputSource, Step::findByShort('high-efficiency-boiler'));
    }
}