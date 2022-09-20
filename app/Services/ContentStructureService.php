<?php

namespace App\Services;

use App\Traits\FluentCaller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ContentStructureService
{
    use FluentCaller;

    protected array $contentStructure;

    public function __construct(array $contentStructure)
    {
        $this->contentStructure = $contentStructure;
    }

    public function applicableForExampleBuildings(): array
    {
        $contentStructure = $this->contentStructure;

        $filterOutUserInterests = function ($key) {
            return false === stristr($key, 'user_interests');
        };

        foreach (Arr::except($contentStructure, ['general-data', 'insulated-glazing', 'ventilation',]) as $stepShort => $structureWithinStep) {
            $contentStructure[$stepShort]['-'] = array_filter($structureWithinStep['-'], $filterOutUserInterests, ARRAY_FILTER_USE_KEY);
        }

        unset(
            $contentStructure['general-data']['building-characteristics']['building_features.building_type_id'],
            $contentStructure['general-data']['building-characteristics']['building_features.build_year'],
            $contentStructure['general-data']['usage']['user_energy_habits.resident_count'],

            $contentStructure['high-efficiency-boiler']['-']['user_energy_habits.amount_gas'],
            $contentStructure['high-efficiency-boiler']['-']['user_energy_habits.amount_electricity'],
            $contentStructure['solar-panels']['-']['user_energy_habits.amount_electricity'],
            $contentStructure['high-efficiency-boiler']['-']['user_energy_habits.resident_count'],

            $contentStructure['heater']['-']['user_energy_habits.water_comfort_id'],
            $contentStructure['solar-panels']['-']['building_pv_panels.total_installed_power']
        );

        // filter out interest stuff from the interest page
        $contentStructure['general-data']['interest'] = array_filter($contentStructure['general-data']['interest'], function ($key) {
            return false === stristr($key, 'user_interest');
        }, ARRAY_FILTER_USE_KEY);

        // Remove general data considerables
        foreach (($contentStructure['general-data']['interest'] ?? []) as $interestField => $interestData) {
            if (Str::endsWith($interestField, 'is_considering')) {
                unset($contentStructure['general-data']['interest'][$interestField]);
            }
        }

        return $contentStructure;
    }
}