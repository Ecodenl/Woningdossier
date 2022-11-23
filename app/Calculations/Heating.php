<?php

namespace App\Calculations;

use App\Deprecation\ToolHelper;
use App\Helpers\Kengetallen;
use App\Helpers\KeyFigures\Heater\KeyFigures;
use App\Models\ComfortLevelTapWater;
use App\Models\HeatPumpCharacteristic;
use App\Models\KeyFigureBoilerEfficiency;
use App\Models\KeyFigureConsumptionTapWater;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            'boiler-setting-comfort-heat',
            'boiler-type'
        );
        $wtwNew     = $this->calculateTapWater(
            'new-heat-source',
            'new-heat-source-warm-tap-water',
            'new-water-comfort',
            'new-heat-pump-type',
            'new-boiler-setting-comfort-heat',
            'new-boiler-type'
        );

        $wtw = [
            'current' => $wtwCurrent,
            'new'     => $wtwNew,
        ];
        Log::debug(__METHOD__." - wtw calculated: ".json_encode($wtw));

        $cooking = [
            'current' => $this->energyUsageForCooking('cook-type'),
            'new'     => $this->energyUsageForCooking('new-cook-type'),
        ];

        Log::debug(__METHOD__." - cooking calculated: ".json_encode($wtw));

        $heatingCurrent = $this->calculateHeating(
            'heat-source',
            'heat-source-warm-tap-water',
            'boiler-type',
            $amountGas = $this->getAnswer('amount-gas') ?? 0,
            $cooking['current'],
            $wtw['current']
        );
        $heatingNew     = $this->calculateHeating(
            'new-heat-source',
            'new-heat-source-warm-tap-water',
            'new-boiler-type',
            data_get($heatingCurrent, 'gas.bruto', 0),
            $cooking['new'],
            $wtw['new']
        );

        $heating = [
            'current' => $heatingCurrent,
            'new'     => $heatingNew,
        ];

        Log::debug(__METHOD__." - heating calculated: ".json_encode($heating));

        // correct wtw heating in the case the heat-source is not hr-boiler or
        // district-heating: gas for heating is 0. wtw = amount gas - cooking
        // because cooking is fairly stable / always around 37, the wtw gas
        // usage should be the leftover of the amount gas - cooking.
        /** @var array $heatSources */
        foreach (
            [
                'current' => 'heat-source',
                'new'     => 'new-heat-source',
            ] as $situation => $heatSourceShort
        ) {
            $heatSources = $this->getAnswer($heatSourceShort) ?? [];

            if ( ! in_array('hr-boiler', $heatSources) && ! in_array(
                    'district-heating',
                    $heatSources
                )) {
                Log::debug(
                    __METHOD__.' - correcting situation (because no hr-boiler / district-heating => assign gas rest to wtw and not to heating'
                );
                $wtw[$situation]['gas'] = $amountGas - $cooking[$situation]['gas'];
            }
        }

        Log::debug(
            "End result: ".json_encode([
                'heating'   => $heating,
                'tap_water' => $wtw,
                'cooking'   => $cooking,
            ])
        );

        return [
            'heating'   => $heating,
            'tap_water' => $wtw,
            'cooking'   => $cooking,
        ];
    }

    protected function calculateHeating(
        string $heatSourceShort,
        string $heatSourceWtwShort,
        string $boilerTypeShort,
        int $amountGas,
        array $cookingUsage,
        array $wtwUsage
    ) {
        // Note electricity will always be 0 at the current stage as we cannot
        // calculate that
        $result = [
            'gas'         => [
                'bruto' => 0,
                'netto' => 0,
            ],
            'electricity' => [
                'bruto' => 0,
                'netto' => 0,
            ],
        ];

        /** @var array $heatSources */
        $heatSources = $this->getAnswer($heatSourceShort) ?? [];
        // if $heatSources does not contain 'hr-boiler', we cannot calculate.
        if ( ! in_array('hr-boiler', $heatSources)) {
            Log::debug(
                __METHOD__.' - No HR boiler, returning'.json_encode($result)
            );

            return $result;
        }

        // Get the primary wtw heating, but: leave out the sun boiler as this is
        // a helper and not the primary heating solution.
        $heatSourcesWtw            = $this->getAnswer($heatSourceWtwShort);
        $primaryWtwHeatSourceShort = Arr::first(
            Arr::except($heatSourcesWtw, 'sun-boiler')
        );

        data_set($result, 'gas.bruto', $amountGas);
        $heatingGasUsage = $amountGas - data_get($cookingUsage, 'gas', 0);
        Log::debug(
            __METHOD__.' - heatingGasUsage = '.$heatingGasUsage.' = '.$amountGas.' - '.data_get(
                $cookingUsage,
                'gas',
                0
            )
        );

        if (in_array(
            $primaryWtwHeatSourceShort,
            ['hr-boiler', 'kitchen-geyser', 'district-heating']
        )) {
            Log::debug(
                __METHOD__.' - hr-boiler/kitchen-geyser/district-heating is primary wtw'
            );
            Log::debug(
                'heatingGasUsage = '.$heatingGasUsage.' - '.data_get(
                    $wtwUsage,
                    'gas.bruto',
                    0
                )
            );
            $heatingGasUsage -= data_get($wtwUsage, 'gas.bruto', 0);

            // default = 97% for HR-107.
            $efficiency = $this->getBoilerKeyFigureEfficiency(
                $boilerTypeShort
            ) ?? 97;
            Log::debug(
                'heatingGasUsage = '.$heatingGasUsage.' * '.$efficiency.' %'
            );
            $heatingGasUsage *= ($efficiency->heating / 100);
        }

        data_set($result, 'gas.netto', $heatingGasUsage);

        return $result;
    }

    protected function calculateTapWater(
        string $heatSourceShort,
        string $heatSourceWtwShort,
        string $waterComfortShort,
        string $heatPumpTypeShort = '',
        string $boilerSettingComfortHeatShort = '',
        string $boilerTypeShort = ''
    ): array {
        $case = Str::contains($heatSourceShort, 'new-') ? 'new' : 'current';
        Log::debug(__METHOD__.' - case: '.$case);

        // Note the energy usage for tap water will be EITHER gas (m3) or
        // electricity (kWh). Which means one of the two array keys will be 0
        // and the other one will be filled in the result.
        $result = [
            'gas'         => [
                'bruto' => 0,
                'netto' => 0,
            ],
            'electricity' => [
                'bruto' => 0,
                'netto' => 0,
            ],
        ];

        /** @var array $heatSources */
        $heatSources = $this->getAnswer($heatSourceShort) ?? [];
        // if $heatSources does not contain 'hr-boiler', we cannot calculate.
        if ( ! in_array('hr-boiler', $heatSources)) {
            Log::debug(
                __METHOD__.' - No HR boiler, returning'.json_encode($result)
            );

            return $result;
        }

        // Get the primary wtw heating, but: leave out the sun boiler as this is
        // a helper and not the primary heating solution.
        $heatSourcesWtw            = $this->getAnswer($heatSourceWtwShort);
        $primaryWtwHeatSourceShort = Arr::first(
            Arr::except($heatSourcesWtw, 'sun-boiler')
        );

        if (empty($primaryWtwHeatSourceShort) || $primaryWtwHeatSourceShort == 'none') {
            Log::debug(
                __METHOD__.' - No primary wtw heat source, returning'.json_encode(
                    $result
                )
            );

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
            Log::debug(
                __METHOD__.' - No key figure for tap water consumption, returning'.json_encode(
                    $result
                )
            );

            return $result;
        }
        $gasUsageWtw = $consumption->energy_consumption;
        Log::debug(__METHOD__.' - gasUsageWtw: '.$gasUsageWtw);

        // solar boiler (deduction) amount comes into play: if there's a solar
        // boiler
        $solarBoilerYield = [
            'gas'         => 0,
            'electricity' => 0,
        ];
        if (in_array('sun-boiler', $heatSourcesWtw)) {
            Log::debug(__METHOD__.' - There is a solar boiler');
            // there is a solar-boiler. Calculate the yield.
            $solarBoilerInfo  = Heater::calculate(
                $this->building,
                $this->inputSource
            );
            $solarBoilerYield = [
                'gas'         => $solarBoilerInfo['savings_gas'],
                'electricity' => $solarBoilerInfo['production_heat'],
            ];
            Log::debug(
                __METHOD__.' - solar boiler yield: '.json_encode(
                    $solarBoilerYield
                )
            );
        }

        Log::debug("primaryWtwHeatSourceShort: " . $primaryWtwHeatSourceShort);
        // 8.972 = Kengetallen::gasKwhPerM3();
        if (in_array(
            $primaryWtwHeatSourceShort,
            ['hr-boiler', 'kitchen-geyser', 'district-heating']
        )) {
            Log::debug(
                __METHOD__.' - primary wtw hr-boiler/kitchen-geyser/district-heating'
            );
            data_set($result, 'gas.bruto', $gasUsageWtw);
            // step 1: Gas usage wtw from table
            $energyConsumption = $gasUsageWtw;

            // step 2: Deduct solar boiler yield (gas)
            $energyConsumption -= data_get($solarBoilerYield, 'gas', 0);

            // default = 89% for HR-107.
            $efficiency = $this->getBoilerKeyFigureEfficiency(
                $boilerTypeShort
            ) ?? 89;
            Log::debug(
                'energyConsumption = '.$energyConsumption.' * '.$efficiency.' %'
            );
            $energyConsumption *= ($efficiency->wtw / 100);

            //$result['gas'] = round($energyConsumption, 4);
            data_set($result, 'gas.netto', round($energyConsumption, 4));
        }
        if (in_array($primaryWtwHeatSourceShort, ['electric-boiler'])) {
            Log::debug(__METHOD__.' - primary wtw electric-boiler');
            // step 1: Gas usage wtw from table * gasKwhPerM3 (m3 -> kWh)
            $energyConsumption = $gasUsageWtw * Kengetallen::gasKwhPerM3();
            data_set($result, 'electricity.bruto', round($energyConsumption, 4));

            // step 2: Deduct solar boiler yield (electricity)
            $energyConsumption -= data_get($solarBoilerYield, 'electricity', 0);

            //$result['electricity'] = round($energyConsumption, 4);
            data_set($result, 'electricity.netto', round($energyConsumption, 4));
        }
        if (in_array(
            $primaryWtwHeatSourceShort,
            ['heat-pump', 'heat-pump-boiler']
        )) {
            Log::debug(__METHOD__.' - primary wtw heat-pump/heat-pump-boiler');
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
            Log::debug(
                __METHOD__.' - characteristic id = '.optional(
                    $characteristics
                )->id
            );
            $scop = 1;
            if ($characteristics instanceof HeatPumpCharacteristic) {
                $scop = max($characteristics, 1); // prevent divide by 0
            }

            Log::debug(__METHOD__.' - scop = '.$scop);

            $energyConsumption = $gasUsageWtw * Kengetallen::gasKwhPerM3(
                ) / $scop;
            data_set($result, 'electricity.bruto', round($energyConsumption, 4));

            // step 2: Deduct solar boiler yield (electricity)
            $energyConsumption -= data_get($solarBoilerYield, 'electricity', 0);

            //$result['electricity'] = round($energyConsumption, 4);
            data_set($result, 'electricity.netto', round($energyConsumption, 4));
        }

        Log::debug(__METHOD__.' - end result: '.json_encode($result));

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

    protected function getBoilerKeyFigureEfficiency(string $boilerTypeShort
    ): ?KeyFigureBoilerEfficiency {
        $boiler = ToolHelper::getServiceValueByCustomValue(
            'boiler',
            $boilerTypeShort,
            $this->getAnswer($boilerTypeShort)
        );
        if ( ! $boiler instanceof ServiceValue) {
            // if even the current boiler wasn't present, the user probably already
            // has a heat pump, so we will calculate with the most efficient boiler
            $boiler = Service::findByShort('boiler')->values()->orderByDesc(
                'calculate_value'
            )->limit(1)->first();
        }

        return $boiler->keyFigureBoilerEfficiency;
    }

}