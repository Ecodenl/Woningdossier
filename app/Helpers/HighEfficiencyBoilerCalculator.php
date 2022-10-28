<?php

namespace App\Helpers;

use App\Deprecation\ToolHelper;
use App\Helpers\KeyFigures\Heater\KeyFigures;
use App\Models\ComfortLevelTapWater;
use App\Models\KeyFigureConsumptionTapWater;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\ToolQuestion;
use App\Models\UserEnergyHabit;
use App\Services\ConditionService;
use Carbon\Carbon;

class HighEfficiencyBoilerCalculator
{
    /**
     * @param ServiceValue|null    $currentBoiler
     * @param UserEnergyHabit|null $habit
     *
     * @return float|int|mixed|null
     */
    public static function calculateGasSavings(?ServiceValue $currentBoiler, UserEnergyHabit $habit)
    {
        $building = $habit->user->building;
        $inputSource = $habit->inputSource;
        $amountGas = $habit->amount_gas;

        // It is possible the user does not have a boiler currently!
        $current = self::calculateGasUsage($currentBoiler, $habit, $amountGas);

        $newBoilerQuestion = ToolQuestion::findByShort('new-boiler-type');
        $conditionService = ConditionService::init()->building($building)->inputSource($inputSource)
            ->forModel($newBoilerQuestion);

        // We will see if the user has a new boiler, and otherwise we will grab the best boiler available.
        $newBoilerType = null;
        if ($conditionService->isViewable() && $conditionService->hasCompletedSteps(['heating'])) {
            $newBoilerType = ToolHelper::getServiceValueByCustomValue(
                'boiler',
                'new-boiler-type',
                $building->getAnswer($inputSource, $newBoilerQuestion)
            );
        }

        if (! $newBoilerType instanceof ServiceValue) {
            $newBoilerType = Service::findByShort('boiler')->values()->orderBy('order', 'desc')->first();
        }

        // now for the new
        $newBoilerEfficiency = $newBoilerType->keyFigureBoilerEfficiency;

        $usage = [
            'heating' => $current['heating']['netto'] / ($newBoilerEfficiency['heating'] / 100),
            'tap_water' => $current['tap_water']['netto'] / ($newBoilerEfficiency['wtw'] / 100),
            'cooking' => $current['cooking'],
        ];

        $usageNew = $usage['heating'] + $usage['tap_water'] + $usage['cooking'];
        // yes, array_sum is a method, but this is easier to compare to the theory
        $result = $amountGas - $usageNew;

        self::debug('Gas usage ( '.$usageNew.' ) with new boiler: '.json_encode($usage));
        self::debug('Results in saving of '.$result.' = '.$amountGas.' - '.$usageNew);

        return $result;
    }

    /**
     * @param ServiceValue|null    $boiler
     * @param UserEnergyHabit|null $habit
     * @param int                  $amountGas
     *
     * @return array
     */
    public static function calculateGasUsage($boiler, $habit, $amountGas = 0)
    {
        // TODO: We don't want to pass a habit. We want to pass dedicated answers, a building and an input source
        $amountGas = $habit->amount_gas ?? $amountGas;

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

        if (! $boiler instanceof ServiceValue) {
            return $result;
        }
        $boilerEfficiency = $boiler->keyFigureBoilerEfficiency;

        self::debug(__METHOD__.' boiler efficiencies of boiler: '.$boilerEfficiency->heating.'% (heating) and '.$boilerEfficiency->wtw.'% (tap water)');

        if ($habit instanceof UserEnergyHabit) {
            $building = $habit->user->building;

            $cookType = $building->getAnswer($habit->inputSource, ToolQuestion::findByShort('cook-type'));

            if ($cookType == "gas") {
                $result['cooking'] = Kengetallen::ENERGY_USAGE_COOK_TYPE_GAS; // m3
            }

            // From solar boiler / heater
            $comfortLevel = $habit->comfortLevelTapWater;
            if ($comfortLevel instanceof ComfortLevelTapWater) {
                $consumption = KeyFigures::getCurrentConsumption($habit->resident_count, $comfortLevel);
                if ($consumption instanceof KeyFigureConsumptionTapWater) {
                    $brutoTapWater = $consumption->energy_consumption;
                }
            }
        }

        // todo use solar boiler gas usage here
        $result['tap_water']['bruto'] = $brutoTapWater ?? 0;
        $result['tap_water']['netto'] = $result['tap_water']['bruto'] * ($boilerEfficiency->wtw / 100);

        $result['heating']['bruto'] = $amountGas - $result['tap_water']['bruto'] - $result['cooking'];
        $result['heating']['netto'] = $result['heating']['bruto'] * ($boilerEfficiency->heating / 100);

        self::debug(__METHOD__.' Gas usage: '.json_encode($result));

        return $result;
    }

    public static function determineApplicationYear(MeasureApplication $measureApplication, $last)
    {
        self::debug(__METHOD__);

        if (! is_numeric($last)) {
            return Carbon::now()->year;
        }

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
        // \Log::debug($line);
    }
}
