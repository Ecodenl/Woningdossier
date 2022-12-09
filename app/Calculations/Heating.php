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

    protected array $boilerShares = [
        'current' => 100,
        'new' => 100,
    ];

    public function useBoilerShares(array $boilerShares): self
    {
        $this->boilerShares = $boilerShares;

        return $this;
    }

    public function performCalculations(): array
    {
        $cooking = [
            'current' => $this->energyUsageForCooking('cook-type'),
            'new'     => $this->energyUsageForCooking('new-cook-type'),
        ];

        Log::debug(str_repeat('-', 80));
        Log::debug(__METHOD__ . ' - CURRENT SITUATION');
        Log::debug(str_repeat('-', 80));

        // calculate first as it might be a factor for heating later on.
        $wtwCurrent = $this->calculateTapWater(
            'heat-source',
            'heat-source-warm-tap-water',
            'water-comfort',
            'heat-pump-type',
            'boiler-setting-comfort-heat',
            'boiler-type',
            $this->getAnswer('amount-gas') ?? 0,
            data_get($cooking, 'current')
        );

        $heatingCurrent = $this->calculateHeating(
            'heat-source',
            'heat-source-warm-tap-water',
            'boiler-type',
            $amountGas = $this->getAnswer('amount-gas') ?? 0,
            $cooking['current'],
            $wtwCurrent,
            'new-heat-pump-type',
            'new-boiler-setting-comfort-heat'
        );

        Log::debug(str_repeat('-', 80));
        Log::debug(__METHOD__ . ' - NEW SITUATION');
        Log::debug(str_repeat('-', 80));

        $wtwNew     = $this->calculateTapWater(
            'new-heat-source',
            'new-heat-source-warm-tap-water',
            'new-water-comfort',
            'new-heat-pump-type',
            'new-boiler-setting-comfort-heat',
            'new-boiler-type',
//            data_get($heatingCurrent, 'gas.netto', 0), // netto!
            0, // only used in current situation
            data_get($cooking, 'new')
        );

        $wtw = [
            'current' => $wtwCurrent,
            'new'     => $wtwNew,
        ];

        $heatingNew     = $this->calculateHeating(
            'new-heat-source',
            'new-heat-source-warm-tap-water',
            'new-boiler-type',
            data_get($heatingCurrent, 'gas.netto', 0), // netto!
            $cooking['new'],
            $wtw['new'],
            'new-heat-pump-type',
            'new-boiler-setting-comfort-heat'
        );

        $heating = [
            'current' => $heatingCurrent,
            'new'     => $heatingNew,
        ];

        // correct wtw heating in the case the heat-source is not hr-boiler or
        // district-heating: gas for heating is 0. wtw = amount gas - cooking
        // because cooking is fairly stable / always around 37, the wtw gas
        // usage should be the leftover of the amount gas - cooking.
        // correction should only be done on the current situation.
        /** @var array $heatSources */
        foreach (
            [
                'current' => 'heat-source',
                //'new'     => 'new-heat-source',
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

        Log::debug(str_repeat('-', 80));
        Log::debug(__METHOD__ . ' - END RESULT');
        Log::debug(str_repeat('-', 80));

        Log::debug(
            "End result: ".json_encode([
                'heating'   => $heating,
                'tap_water' => $wtw,
                'cooking'   => $cooking,
            ])
        );

        Log::debug(str_repeat('-', 80));

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
        array $wtwUsage,
        string $heatPumpTypeShort = '',
        string $boilerSettingComfortHeatShort = ''
    ) {
        // either 'new' or 'current'
        $case = Str::contains($heatSourceShort, 'new-') ? 'new' : 'current';
        Log::debug(__METHOD__.' - case: '.$case);

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

        // ---------------------------------------------------------------------
        // Some cases we can't cover yet
        // ---------------------------------------------------------------------

        /** @var array $heatSources */
        $heatSources = $this->getAnswer($heatSourceShort) ?? [];

        if ( ! in_array('hr-boiler', $heatSources) && ! in_array(
                'district-heating',
                $heatSources
            )) {
            Log::debug(__METHOD__.' - No HR boiler or district heating');
            // we can only handle heat pump for 'new' case. We bail if 'current'
            // in the other cases we can't calculate anything yet, so we bail
            // always.
            if ( ! in_array('heat-pump', $heatSources)) {
                Log::debug(
                    __METHOD__.' - no heat pump, no hr-boiler, no district-heating, cannot calculate, so all is 0.'
                );

                return $result;
            }
            // we can only handle heat pump for 'new' case. We bail if 'current'
            if ($case === 'current') {
                Log::debug(
                    __METHOD__.' - heat pump for current situation. Cannot calculate, so all is 0.'
                );

                return $result;
            }
        }

        // ---------------------------------------------------------------------
        // Some cases we can't cover yet (end)
        // ---------------------------------------------------------------------

        $heatingGasUsage = $amountGas - data_get($cookingUsage, 'gas', 0);
        Log::debug(
            __METHOD__.' - heatingGasUsage = '.$heatingGasUsage.' = '.$amountGas.' - '.data_get(
                $cookingUsage,
                'gas',
                0
            )
        );

        // district heating or HR boiler
        if (in_array('district-heating', $heatSources) ||
            (in_array('hr-boiler', $heatSources) && ! in_array(
                    'heat-pump',
                    $heatSources
                ))) {
            Log::debug(__METHOD__.' - district heating or HR boiler');
            if ($case === 'current') {
                $energyConsumption = $amountGas;
                $energyConsumption -= data_get($cookingUsage, 'gas', 0);
                $energyConsumption -= data_get($wtwUsage, 'gas.bruto', 0);

                data_set($result, 'gas.bruto', $energyConsumption);
                // netto = bruto * efficiency
                // default = 97% for HR-107.
                $efficiency = $this->getBoilerKeyFigureEfficiency(
                    $boilerTypeShort
                ) ?? 97;
                Log::debug(__METHOD__ . ' - energyConsumption = '.$energyConsumption.' * '.$efficiency->heating.' %'
                );
                $energyConsumption *= ($efficiency->heating / 100);

                data_set($result, 'gas.netto', round($energyConsumption, 4));
            } else {
                $energyConsumption = $amountGas;
                // bruto = energy consumption / efficiency
                // default = 97% for HR-107.
                $efficiency        = $this->getBoilerKeyFigureEfficiency(
                    $boilerTypeShort
                ) ?? 97;
                $energyConsumption /= ($efficiency->heating / 100);

                data_set($result, 'gas.bruto', round($energyConsumption));

                // netto = bruto * efficiency (basically the energy consumption..)
                $energyConsumption *= ($efficiency->heating / 100);

                data_set($result, 'gas.netto', round($energyConsumption, 4));
            }
        }

        if (in_array('heat-pump', $heatSources)) {
            // always needed:
            if ($case === 'new') {
                $heatPumpConfigurable = ToolHelper::getServiceValueByCustomValue(
                    'heat-pump',
                    $heatPumpTypeShort,
                    $this->getAnswer($heatPumpTypeShort)
                );
            } else {
                $heatPumpConfigurable = ServiceValue::find(
                    $this->getAnswer($heatPumpTypeShort)
                );
            }

            $heatingTemperature = ToolQuestion::findByShort(
                'new-boiler-setting-comfort-heat'
            )
                                              ->toolQuestionCustomValues()
                                              ->whereShort(
                                                  $this->getAnswer(
                                                      $boilerSettingComfortHeatShort
                                                  )
                                              )
                                              ->first();


            if (in_array('hr-boiler', $heatSources)) {
                Log::debug(__METHOD__.' - Hybrid heat pump');
                // just (double) checking
                if ($case === 'new') {
                    // gas
                    Log::debug(__METHOD__.' - new situation');
                    $energyConsumption = $amountGas;
                    // bruto = energy consumption / efficiency
                    // default = 97% for HR-107.
                    $efficiency        = $this->getBoilerKeyFigureEfficiency(
                        $boilerTypeShort
                    ) ?? 97;
                    Log::debug(__METHOD__ . " - Bruto(gas) = $energyConsumption / ($efficiency->heating / 100)");
                    $energyConsumption /= ($efficiency->heating / 100);
                    Log::debug(__METHOD__ . " - Bruto(gas) = $energyConsumption");

                    // use the boiler share here
                    $shareHeating = data_get($this->boilerShares, $case, 100);

                    $energyConsumption = $energyConsumption * ((100 - $shareHeating) / 100);

                    data_set($result, 'gas.bruto', round($energyConsumption));

                    Log::debug(__METHOD__ . " - Netto(gas) = $energyConsumption * ($efficiency->heating / 100)");
                    // netto = bruto * efficiency (basically the energy consumption..)
                    $energyConsumption *= ($efficiency->heating / 100);
                    Log::debug(__METHOD__ . " - Netto(gas) = $energyConsumption");

                    data_set(
                        $result,
                        'gas.netto',
                        round($energyConsumption, 4)
                    );

                    // electricity
                    // energy demand = gas usage - netto gas delivery
                    $energyDemand = $amountGas - $energyConsumption;
                    Log::debug(__METHOD__ . " - Bruto(electricity) Energy demand = $amountGas - $energyConsumption = " . $energyDemand . " m3");
                    // energyDemand from m3 to kWh
                    $energyDemand *= Kengetallen::gasKwhPerM3();
                    Log::debug(__METHOD__ . " - Bruto(electricity) Energy demand = $energyDemand kWh");

                    data_set($result, 'electricity.bruto', $energyDemand);

                    // netto = bruto / scop
                    $characteristics = $this->lookupHeatPumpCharacteristics(
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
                        $scop = max(
                            $characteristics->scop,
                            1
                        ); // prevent divide by 0
                    }

                    Log::debug(__METHOD__ . " - Netto(electricity) = $energyDemand / $scop");
                    $energyDemand /= $scop;
                    Log::debug(__METHOD__ . " - Netto(electricity) = $energyDemand kWh");

                    data_set(
                        $result,
                        'electricity.netto',
                        round($energyDemand, 4)
                    );
                }
            } else {
                Log::debug(__METHOD__.' - Full heat pump');
                // just (double) checking
                if ($case === 'new') {
                    $energyDemand = $amountGas;
                    Log::debug(__METHOD__ . " - Bruto(electricity) Energy demand = " . $energyDemand . " m3");
                    // energyDemand from m3 to kWh
                    $energyDemand *= Kengetallen::gasKwhPerM3();
                    Log::debug(__METHOD__ . " - Bruto(electricity) Energy demand = $energyDemand kWh");

                    data_set($result, 'electricity.bruto', $energyDemand);

                    // netto = bruto / scop
                    $characteristics = $this->lookupHeatPumpCharacteristics(
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
                        $scop = max(
                            $characteristics->scop,
                            1
                        ); // prevent divide by 0
                    }

                    Log::debug(__METHOD__ . " - Netto(electricity) = $energyDemand / $scop");
                    $energyDemand /= $scop;
                    Log::debug(__METHOD__ . " - Netto(electricity) = $energyDemand kWh");

                    data_set(
                        $result,
                        'electricity.netto',
                        round($energyDemand, 4)
                    );
                }
            }
        }


        /*
        // Get the primary wtw heating, but: leave out the sun boiler as this is
        // a helper and not the primary heating solution.
        $heatSourcesWtw            = $this->getAnswer($heatSourceWtwShort);
        $primaryWtwHeatSourceShort = Arr::first(
            Arr::except($heatSourcesWtw, 'sun-boiler')
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
                data_set($result, 'gas.bruto', $amountGas);


                data_set($result, 'gas.netto', $heatingGasUsage);
        */

        return $result;
    }

    // CHECKED.
    protected function calculateTapWater(
        string $heatSourceShort,
        string $heatSourceWtwShort,
        string $waterComfortShort,
        string $heatPumpTypeShort = '',
        string $boilerSettingComfortHeatShort = '',
        string $boilerTypeShort = '',
        int $amountGas = 0,
        array $cookingUsage = []
    ): array {
        // either 'new' or 'current'
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

        // Get the primary wtw heating, but: leave out the sun boiler as this is
        // a helper and not the primary heating solution.
        $heatSourcesWtw            = $this->getAnswer($heatSourceWtwShort);
        $primaryWtwHeatSourceShort = Arr::first(
            Arr::except($heatSourcesWtw, 'sun-boiler')
        );

        if (empty($primaryWtwHeatSourceShort) || $primaryWtwHeatSourceShort == 'none') {
            Log::debug(__METHOD__.' - No primary wtw heat source, returning 0\'s');

            return $result;
        }

        // needed on every calculation: the gasUsageWtw.
        // Note: When there's no gas-based heating source, but there is a gas-based
        // heating source for tap water, the gasUsage will NOT be using this
        // table-defined value later on!
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
        Log::debug(__METHOD__.' - gasUsageWtw from tables: '.$gasUsageWtw);

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

        // 8.972 = Kengetallen::gasKwhPerM3();
        if (in_array(
            $primaryWtwHeatSourceShort,
            ['hr-boiler', 'kitchen-geyser', 'district-heating']
        )) {
            Log::debug(
                __METHOD__.' - primary wtw: hr-boiler/kitchen-geyser/district-heating'
            );
            // Check the
            /** @var array $heatSources */
            $heatSources = $this->getAnswer($heatSourceShort) ?? [];

            // if there's no gas-based heating source, but there is gas-based tap water
            // we can only do this in the current situation as the new situation
            // would contain a cyclic dependency.
            if ($case === 'current' && ! in_array('hr-boiler', $heatSources) && ! in_array(
                    'district-heating',
                    $heatSources
                ) && $amountGas > 0) {
                Log::debug(__METHOD__.' - Gas based wtw, but no gas-based heating. We will use the amountGas - cooking(gas) instead of the table-values');
                $gasUsageWtw = $amountGas - data_get($cookingUsage, 'gas', 0);

            }
            // step 1: Gas usage wtw from table
            $energyConsumption = $gasUsageWtw;

            // step 2: Deduct solar boiler yield (gas)
            $energyConsumption -= data_get($solarBoilerYield, 'gas', 0);

            // bruto = gas usage - solar boiler yield
            data_set($result, 'gas.bruto', $energyConsumption);

            // netto = bruto * efficiency
            // default = 89% for HR-107.
            $efficiency = $this->getBoilerKeyFigureEfficiency(
                $boilerTypeShort
            ) ?? 89;
            Log::debug(
                'energyConsumption = '.$energyConsumption.' * '.$efficiency->wtw.' %'
            );
            $energyConsumption *= ($efficiency->wtw / 100);

            data_set($result, 'gas.netto', round($energyConsumption, 4));
        }
        if (in_array($primaryWtwHeatSourceShort, ['electric-boiler'])) {
            Log::debug(__METHOD__.' - primary wtw electric-boiler');
            // step 1: Gas usage wtw from table * gasKwhPerM3 (m3 -> kWh)
            $energyConsumption = $gasUsageWtw * Kengetallen::gasKwhPerM3();

            // step 2: Deduct solar boiler yield (electricity)
            $energyConsumption -= data_get($solarBoilerYield, 'electricity', 0);

            // bruto = electricity usage - solar boiler yield
            data_set(
                $result,
                'electricity.bruto',
                round($energyConsumption, 4)
            );

            // netto = bruto * efficiency
            // currently the efficiency for electri boiler is not defined / set to 100%.
            $efficiency = 100;

            $energyConsumption *= ($efficiency / 100);

            data_set(
                $result,
                'electricity.netto',
                round($energyConsumption, 4)
            );
        }
        if (in_array(
            $primaryWtwHeatSourceShort,
            ['heat-pump', 'heat-pump-boiler']
        )) {
            Log::debug(__METHOD__.' - primary wtw heat-pump/heat-pump-boiler');

            if ($case === 'new') {
                $heatPumpConfigurable = ToolHelper::getServiceValueByCustomValue(
                    'heat-pump',
                    $heatPumpTypeShort,
                    $this->getAnswer($heatPumpTypeShort)
                );
            } else {
                $heatPumpConfigurable = ServiceValue::find(
                    $this->getAnswer($heatPumpTypeShort)
                );
            }

            $heatingTemperature = ToolQuestion::findByShort(
                'new-boiler-setting-comfort-heat'
            )
                                              ->toolQuestionCustomValues()
                                              ->whereShort(
                                                  $this->getAnswer(
                                                      $boilerSettingComfortHeatShort
                                                  )
                                              )
                                              ->first();

            $characteristics = $this->lookupHeatPumpCharacteristics(
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
                $scop = max(
                    $characteristics->scop_tap_water,
                    1
                ); // prevent divide by 0
            }

            Log::debug(__METHOD__.' - scop = '.$scop);

            // step 1: Gas usage wtw from table * gasKwhPerM3 (m3 -> kWh)
            $energyConsumption = $gasUsageWtw * Kengetallen::gasKwhPerM3();

            // step 2: Deduct solar boiler yield (electricity)
            $energyConsumption -= data_get($solarBoilerYield, 'electricity', 0);

            // bruto = gas usage * 8.972 - solar boiler yield
            data_set(
                $result,
                'electricity.bruto',
                round($energyConsumption, 4)
            );

            // netto = bruto / scop
            $energyConsumption /= $scop;

            data_set(
                $result,
                'electricity.netto',
                round($energyConsumption, 4)
            );
        }

        return $result;
    }

    // CHECKED.
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

    // CHECKED.
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

    // CHECKED.
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