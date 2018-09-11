<?php

namespace App\Helpers;

use App\Models\MeasureApplication;
use App\Models\ServiceValue;
use App\Models\UserEnergyHabit;
use Carbon\Carbon;

class HighEfficiencyBoilerCalculator
{
    public static function calculateGasSavings(ServiceValue $currentBoiler, UserEnergyHabit $habit, $amountGas = null)
    {
        $current = self::calculateGasUsage($currentBoiler, $habit, $amountGas);
        $amountGas = is_null($amountGas) ? $habit->amount_gas : $amountGas;

        // now for the new
        $bestBoiler = ServiceValue::where('service_id', $currentBoiler->service_id)->orderBy('order', 'desc')->first();
        $bestBoilerEfficiency = $bestBoiler->keyFigureBoilerEfficiency;

        $usage = [
            'heating' => 0,
            'tap_water' => 0,
            'cooking' => 0,
        ];
        $usage['heating'] = $current['heating']['netto'] / ($bestBoilerEfficiency['heating'] / 100);
        $usage['tap_water'] = $current['tap_water']['netto'] / ($bestBoilerEfficiency['wtw'] / 100);
        $usage['cooking'] = $current['cooking'];

        $usageNew = $usage['heating'] + $usage['tap_water'] + $usage['cooking'];
        // yes, array_sum is a method, but this is easier to compare to the theory
        $result = $amountGas - $usageNew;

        self::debug('Gas usage ( '.$usageNew.' ) with best boiler: '.json_encode($usage));
        self::debug('Results in saving of '.$result.' = '.$amountGas.' - '.$usageNew);

        return $result;
    }

    // todo solar boiler should be put in this formula as well
    public static function calculateGasUsage(ServiceValue $boiler, UserEnergyHabit $habit, $amountGas = null)
    {
        $amountGas = is_null($amountGas) ? $habit->amount_gas : $amountGas;

        $result = [
            'heating' => [
                'bruto' => 0,
                'netto' => 0,
            ],
            'tap_water' => [
                'bruto' => 0,
                'netto' => 0,
            ],
            'cooking' => 0,
        ];

        $boilerEfficiency = $boiler->keyFigureBoilerEfficiency;

        if (1 == $habit->cook_gas) {
            $result['cooking'] = 65; // m3
        }

        // todo use solar boiler gas usage here
        $result['tap_water']['bruto'] = 0;
        $result['tap_water']['netto'] = $result['tap_water']['bruto'] * ($boilerEfficiency['wtw'] / 100);

        $result['heating']['bruto'] = $amountGas - $result['tap_water']['bruto'] - $result['cooking'];
        $result['heating']['netto'] = $result['heating']['bruto'] * ($boilerEfficiency['heating'] / 100);

        self::debug('Gas usage: '.json_encode($result));

        return $result;
    }

    public static function determineApplicationYear(MeasureApplication $measureApplication, $last)
    {
        self::debug(__METHOD__);

        if ($last + $measureApplication->maintenance_interval <= Carbon::now()->year) {
            self::debug('Last replace is longer than '.$measureApplication->maintenance_interval.' years ago.');
            $year = Carbon::now()->year;
        } else {
            $year = $last + $measureApplication->maintenance_interval;
        }

        return $year;
    }

    protected static function debug($line)
    {
        \Log::debug($line);
    }
}
