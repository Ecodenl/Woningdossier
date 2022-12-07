<?php

namespace App\Helpers;

use App\Deprecation\ToolHelper;
use App\Helpers\KeyFigures\Heater\KeyFigures;
use App\Models\Building;
use App\Models\ComfortLevelTapWater;
use App\Models\InputSource;
use App\Models\KeyFigureConsumptionTapWater;
use App\Models\MeasureApplication;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Traits\FluentCaller;
use App\Traits\HasDynamicAnswers;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class HighEfficiencyBoilerCalculator
{
    use FluentCaller,
        HasDynamicAnswers;

    /**
     * @param  \App\Models\Building  $building
     * @param  \App\Models\InputSource  $inputSource
     * @param  \Illuminate\Support\Collection|null  $answers
     */
    public function __construct(Building $building, InputSource $inputSource, ?Collection $answers = null)
    {
        $this->building = $building;
        $this->inputSource = $inputSource;
        $this->answers = $answers;
    }

    /**
     * @return mixed
     */
    public function calculateGasSavings()
    {
        // It is possible the user does not have a boiler currently!
        $current = $this->calculateGasUsage();
        $amountGas = $this->getAnswer('amount-gas');

        // We will see if the user has a new boiler, and otherwise we will grab the best boiler available.
        $newBoilerType = ToolHelper::getServiceValueByCustomValue(
            'boiler',
            'new-boiler-type',
            $this->getAnswer('new-boiler-type')
        );

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

        $this->debug('Gas usage ( '.$usageNew.' ) with new boiler: '.json_encode($usage));
        $this->debug('Results in saving of '.$result.' = '.$amountGas.' - '.$usageNew);

        return $result;
    }

    /**
     * @return array
     */
    public function calculateGasUsage(): array
    {
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

        $boiler = ServiceValue::find($this->getAnswer('boiler-type'));

        if (! $boiler instanceof ServiceValue) {
            return $result;
        }
        $boilerEfficiency = $boiler->keyFigureBoilerEfficiency;

        $this->debug(__METHOD__.' boiler efficiencies of boiler: '.$boilerEfficiency->heating.'% (heating) and '.$boilerEfficiency->wtw.'% (tap water)');

        $cookType = $this->getAnswer('cook-type');

        if ($cookType == "gas") {
            $result['cooking'] = Kengetallen::ENERGY_USAGE_COOK_TYPE_GAS; // m3
        }

        $comfortLevelTapWater = ComfortLevelTapWater::find($this->getAnswer('water-comfort'));

        // From solar boiler / heater
        if ($comfortLevelTapWater instanceof ComfortLevelTapWater) {
            $consumption = KeyFigures::getCurrentConsumption($this->getAnswer('resident-count'), $comfortLevelTapWater);
            if ($consumption instanceof KeyFigureConsumptionTapWater) {
                $brutoTapWater = $consumption->energy_consumption;
            }
        }

        $amountGas = $this->getAnswer('amount-gas');

        // todo use solar boiler gas usage here
        $result['tap_water']['bruto'] = $brutoTapWater ?? 0;
        $result['tap_water']['netto'] = $result['tap_water']['bruto'] * ($boilerEfficiency->wtw / 100);

        $result['heating']['bruto'] = $amountGas - $result['tap_water']['bruto'] - $result['cooking'];
        $result['heating']['netto'] = $result['heating']['bruto'] * ($boilerEfficiency->heating / 100);

        $this->debug(__METHOD__.' Gas usage: '.json_encode($result));

        return $result;
    }

    /**
     * @param \App\Models\MeasureApplication $measureApplication
     * @param $last
     *
     * @return float|int|string
     */
    public function determineApplicationYear(MeasureApplication $measureApplication, $last)
    {
        $this->debug(__METHOD__);

        if (! is_numeric($last) || $last === 0) {
            return Carbon::now()->year;
        }

        if ($last + $measureApplication->maintenance_interval <= Carbon::now()->year) {
            $this->debug('Last replace is longer than '.$measureApplication->maintenance_interval.' years ago.');
            $year = Carbon::now()->year;
        } else {
            $year = $last + $measureApplication->maintenance_interval;
        }

        return $year;
    }

    protected function debug($line)
    {
        // \Log::debug($line);
    }
}
