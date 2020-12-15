<?php


namespace App\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class ContentHelper
{

    const DECIMALS_BY_NAME = [
        2 => [
            // Surfaces
            'surface',
            'm2',

            // Gebruik
            'user_energy_habits.thermostat_high', // Temperatuur hoge stand
            'user_energy_habits.thermostat_low', // Temperatuur lage stand
            'user_energy_habits.amount_electricity', // Elektragebruik
            'user_energy_habits.amount_gas', // Gasgebruik
        ],
        1 => [
            // Gebruik
            'user_energy_habits.hours_high', // Uren per dag hoge stand
        ],
        0 => [
            // Years
            'year',
            'date',

            // Gebouwkenmerken
            'building_features.building_layers', // Bouwlagen

            // Huidige staat
            'service.7.extra.value', // Zonnepanelen
            'building_pv_panels.total_installed_power', // Totaal vermogen

            // Isolerende beglazing
            'building_insulated_glazings.8.windows', // Plaatsen van, aantal te vervangen ruiten (HR++, geen kozijn)
            'building_insulated_glazings.9.windows', // Plaatsen van, aantal te vervangen ruiten (HR++, met kozijn)
            'building_insulated_glazings.10.windows', // Plaatsen van, aantal te vervangen ruiten (HR, met kozijn)
            'building_insulated_glazings.7.windows', // Aantal te vervangen ruiten (glas in lood)

            // Zonnepanelen
            'building_pv_panels.number', // Hoeveel
        ],
    ];

    /**
     * Formats the content (currently just numbers to 2 decimal places)
     *
     * @param $content
     * @return array
     */
    public static function formatContent($content)
    {
        $dotted = Arr::dot($content);

        foreach ($dotted as $name => $value){

            if (static::isNumeric($name)) {
                // If it's not null, the form request will have validated the value to be numeric
                if (!is_null($value)) {
                    // Format to not use thousand separators
                    $dotted[$name] = NumberFormatter::mathableFormat($value, static::getDecimals($name));
                }
            }
        }


        dd(\App\Helpers\Arr::arrayUndot($dotted));
    }

    /**
     * Check if a name is part of the content that needs to be evaluated as numeric
     */
    public static function isNumeric(string $name): bool
    {
        $numberFields = array_merge(...self::DECIMALS_BY_NAME);

        if (Str::endsWith($name, $numberFields)) {
            return true;
        }

        return false;
    }

    /**
     * Gets the amount of decimals per name (e.g. a year has no decimals)
     * @param $name
     * @return int
     */
    public static function getDecimals($name)
    {
        $decimalsByName = self::DECIMALS_BY_NAME;

        foreach ($decimalsByName as $decimal => $decimalNames)
        {
            if (Str::endsWith($name, $decimalNames)) {
                return $decimal;
            }
        }

        return 2;
    }
}