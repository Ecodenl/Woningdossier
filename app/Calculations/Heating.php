<?php

namespace App\Calculations;

use App\Deprecation\ToolHelper;
use App\Helpers\Kengetallen;
use App\Helpers\KeyFigures\Heater\KeyFigures;
use App\Models\ComfortLevelTapWater;
use App\Models\HeatPumpCharacteristic;
use App\Models\KeyFigureConsumptionTapWater;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Heating extends Calculator
{


    public function performCalculations(): array
    {
        // calculate first as it might be a factor for heating later on.
        $wtwCurrent = $this->calculateTapWater(
            'heat-source',
            'heat-source-warm-tap-water',
            'water-comfort',
            'heat-pump-type',
            'boiler-setting-comfort-heat'
        );
        $wtwNew     = $this->calculateTapWater(
            'new-heat-source',
            'new-heat-source-warm-tap-water',
            'new-water-comfort',
            'new-heat-pump-type',
            'new-boiler-setting-comfort-heat'
        );

        $wtw = [
            'current' => $wtwCurrent,
            'new'     => $wtwNew,
        ];

        $cooking = [
            'current' => $this->energyUsageForCooking('cook-type'),
            'new'     => $this->energyUsageForCooking('new-cook-type'),
        ];

        $heatingCurrent = $this->calculateHeating(
            'heat-source',
            'heat-source-warm-tap-water',
            $amountGas = $this->getAnswer('amount-gas') ?? 0,
            $cooking['current'],
            $wtw['current']
        );
        $heatingNew     = $this->calculateHeating(
            'new-heat-source',
            'new-heat-source-warm-tap-water',
            data_get($heatingCurrent, 'gas', 0),
            $cooking['new'],
            $wtw['new']
        );

        $heating = [
            'current' => $heatingCurrent,
            'new'     => $heatingNew,
        ];

        return [
            'heating'   => $heating,
            'tap_water' => $wtw,
            'cooking'   => $cooking,
        ];
    }

    protected function calculateHeating(
        string $heatSourceShort,
        string $heatSourceWtwShort,
        int $amountGas,
        array $cookingUsage,
        array $wtwUsage
    ) {
        // Note electricity will always be 0 at the current stage as we cannot
        // calculate that
        $result = [
            'gas'         => 0,
            'electricity' => 0,
        ];

        /** @var array $heatSources */
        $heatSources = $this->getAnswer($heatSourceShort) ?? [];
        // if $heatSources does not contain 'hr-boiler', we cannot calculate.
        if ( ! in_array('hr-boiler', $heatSources)) {
            return $result;
        }

        // Get the primary wtw heating, but: leave out the sun boiler as this is
        // a helper and not the primary heating solution.
        $heatSourcesWtw            = $this->getAnswer($heatSourceWtwShort);
        $primaryWtwHeatSourceShort = Arr::first(
            Arr::except($heatSourcesWtw, 'sun-boiler')
        );

        $heatingGasUsage = $amountGas - data_get($cookingUsage, 'gas', 0);

        if (in_array(
            $primaryWtwHeatSourceShort,
            ['hr-boiler', 'kitchen-geyser', 'district-heating']
        )) {
            $heatingGasUsage -= data_get($wtwUsage, 'gas', 0);
        }

        data_set($result, 'gas', $heatingGasUsage);

        return $result;
    }

    protected function calculateTapWater(
        string $heatSourceShort,
        string $heatSourceWtwShort,
        string $waterComfortShort,
        string $heatPumpTypeShort = '',
        string $boilerSettingComfortHeatShort = ''
    ): array {
        // Note the energy usage for tap water will be EITHER gas (m3) or
        // electricity (kWh). Which means one of the two array keys will be 0
        // and the other one will be filled in the result.
        $result = [
            'gas'         => 0,
            'electricity' => 0,
        ];

        /** @var array $heatSources */
        $heatSources = $this->getAnswer($heatSourceShort) ?? [];
        // if $heatSources does not contain 'hr-boiler', we cannot calculate.
        if ( ! in_array('hr-boiler', $heatSources)) {
            return $result;
        }

        // Get the primary wtw heating, but: leave out the sun boiler as this is
        // a helper and not the primary heating solution.
        $heatSourcesWtw            = $this->getAnswer($heatSourceWtwShort);
        $primaryWtwHeatSourceShort = Arr::first(
            Arr::except($heatSourcesWtw, 'sun-boiler')
        );

        if (empty($primaryWtwHeatSourceShort) || $primaryWtwHeatSourceShort == 'none') {
            return $result;
        }

        // needed on every calculation: the gasUsageWtw.
        $residentCount         = $this->getAnswer('resident-count') ?? 1;
        $comfortLevelSomething = $this->getAnswer(
            $waterComfortShort
        ) ?? 'standard';

        $map            = [
            'standard'          => 1,
            'comfortable'       => 2,
            'extra-comfortable' => 3,
        ];
        $comfortLevelId = $comfortLevelSomething;
        if (array_key_exists($comfortLevelSomething, $map)) {
            $comfortLevelId = $map[$comfortLevelSomething] ?? 0;
        }
        $comfortLevelTapWater = ComfortLevelTapWater::find($comfortLevelId);
        $consumption          = KeyFigures::getCurrentConsumption(
            $residentCount,
            $comfortLevelTapWater
        );
        if ( ! $consumption instanceof KeyFigureConsumptionTapWater) {
            return $result;
        }
        $gasUsageWtw = $consumption->energy_consumption;

        // solar boiler (deduction) amount comes into play: if there's a solar
        // boiler
        $solarBoilerYield = [
            'gas'         => 0,
            'electricity' => 0,
        ];
        if (in_array('sun-boiler', $heatSourcesWtw)) {
            // there is a solar-boiler. Calculate the yield.
            $solarBoilerInfo  = Heater::calculate(
                $this->building,
                $this->inputSource
            );
            $solarBoilerYield = [
                'gas'         => $solarBoilerInfo['savings_gas'],
                'electricity' => $solarBoilerInfo['production_heat'],
            ];
        }

        // 8.972 = Kengetallen::gasKwhPerM3();
        if (in_array(
            $primaryWtwHeatSourceShort,
            ['hr-boiler', 'kitchen-geyser', 'district-heating']
        )) {
            // step 1: Gas usage wtw from table
            $energyConsumption = $gasUsageWtw;

            // step 2: Deduct solar boiler yield (gas)
            $energyConsumption -= data_get($solarBoilerYield, 'gas', 0);

            $result['gas'] = round($energyConsumption, 4);
        }
        if (in_array($primaryWtwHeatSourceShort, ['electric-boiler'])) {
            // step 1: Gas usage wtw from table * gasKwhPerM3 (m3 -> kWh)
            $energyConsumption = $gasUsageWtw * Kengetallen::gasKwhPerM3();

            // step 2: Deduct solar boiler yield (electricity)
            $energyConsumption -= data_get($solarBoilerYield, 'electricity', 0);

            $result['electricity'] = round($energyConsumption, 4);
        }
        if (in_array(
            $primaryWtwHeatSourceShort,
            ['heat-pump', 'heat-pump-boiler']
        )) {
            // step 1: Gas usage wtw from table * gasKwhPerM3 / SCOP (m3 -> kWh)
            $heatPumpConfigurable = ToolHelper::getServiceValueByCustomValue(
                'heat-pump',
                $heatPumpTypeShort,
                $this->getAnswer($heatPumpTypeShort)
            );
            $heatingTemperature   = ToolQuestion::findByShort(
                $boilerSettingComfortHeatShort
            )
                                                ->toolQuestionCustomValues()
                                                ->whereShort(
                                                    $this->getAnswer(
                                                        $boilerSettingComfortHeatShort
                                                    )
                                                )
                                                ->first();
            $characteristics      = $this->lookupHeatPumpCharacteristics(
                $heatPumpConfigurable,
                $heatingTemperature
            );
            $scop                 = 1;
            if ($characteristics instanceof HeatPumpCharacteristic) {
                $scop = max($characteristics, 1); // prevent divide by 0
            }

            $energyConsumption = $gasUsageWtw * Kengetallen::gasKwhPerM3(
                ) / $scop;

            // step 2: Deduct solar boiler yield (electricity)
            $energyConsumption -= data_get($solarBoilerYield, 'electricity', 0);

            $result['electricity'] = round($energyConsumption, 4);
        }

        return $result;
    }

    public function lookupHeatPumpCharacteristics(
        ?Model $heatPumpConfigurable,
        ?ToolQuestionCustomValue $heatingTemperature
    ): ?HeatPumpCharacteristic {
        if ($heatPumpConfigurable instanceof Model && $heatingTemperature instanceof ToolQuestionCustomValue) {
            $key = sprintf(
                'heat-pump-characteristics-%s-%s',
                $heatPumpConfigurable->id,
                $heatingTemperature->id
            );
            $ttl = 60 * 60 * 24; // 24 hours

            return cache()->remember(
                $key,
                $ttl,
                function () use ($heatingTemperature, $heatPumpConfigurable) {
                    return HeatPumpCharacteristic::forHeatPumpConfigurable(
                        $heatPumpConfigurable
                    )
                                                 ->forHeatingTemperature(
                                                     $heatingTemperature
                                                 )
                                                 ->first();
                }
            );
        }

        return null;
    }

    protected function energyUsageForCooking(string $toolQuestionShort): array
    {
        $cookType = $this->getAnswer($toolQuestionShort);

        switch ($cookType) {
            case 'electric':
                return [
                    'gas'         => 0,
                    'electricity' => Kengetallen::ENERGY_USAGE_COOK_TYPE_ELECTRIC,
                ];
            case 'induction':
                return [
                    'gas'         => 0,
                    'electricity' => Kengetallen::ENERGY_USAGE_COOK_TYPE_INDUCTION,
                ];
            default:
                // gas is the default
                return [
                    'gas'         => Kengetallen::ENERGY_USAGE_COOK_TYPE_GAS,
                    'electricity' => 0,
                ];
        }
    }

}