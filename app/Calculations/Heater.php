<?php

namespace App\Calculations;

use App\Deprecation\ToolHelper;
use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\RawCalculator;
use App\Helpers\Kengetallen;
use App\Helpers\KeyFigures\Heater\KeyFigures;
use App\Models\ComfortLevelTapWater;
use App\Models\HeaterComponentCost;
use App\Models\KeyFigureConsumptionTapWater;
use App\Models\PvPanelLocationFactor;
use App\Models\PvPanelOrientation;
use App\Models\PvPanelYield;
use App\Services\CalculatorService;
use Carbon\Carbon;

class Heater extends Calculator
{
    public function performCalculations(): array
    {
        $result = [
            'consumption' => [
                'water' => 0,
                'gas' => 0,
            ],
            'specs' => [
                'size_boiler' => 0,
                'size_collector' => 0,
            ],
            'year' => null,
            'production_heat' => 0,
            'percentage_consumption' => 0,
            'amount_gas' => 0,
            'amount_electricity' => 0,
            'savings_gas' => 0,
            'savings_co2' => 0,
            'savings_money' => 0,
            'cost_indication' => 0,
            'interest_comparable' => 0,
        ];

        $comfortLevel = ToolHelper::getModelByCustomValue(
            ComfortLevelTapWater::query(),
            'new-water-comfort',
            $this->getAnswer('new-water-comfort'),
        );

        if ($comfortLevel instanceof ComfortLevelTapWater) {
            $result['amount_gas'] = $this->getAnswer('amount-gas');
            $result['amount_electricity'] = $this->getAnswer('amount-electricity');

            $consumption = KeyFigures::getCurrentConsumption($this->getAnswer('resident-count'), $comfortLevel);
            if ($consumption instanceof KeyFigureConsumptionTapWater) {
                $result['consumption'] = [
                    'water' => $consumption->water_consumption,
                    'gas' => $consumption->energy_consumption,
                ];
            }
            // \Log::debug('Heater: Current consumption: '.json_encode($result['consumption']));

            $angle = $this->getAnswer('heater-pv-panel-angle') ?? 0;
            $orientationId = $this->getAnswer('heater-pv-panel-orientation') ?? 0;
            $orientation = PvPanelOrientation::find($orientationId);

            $helpFactor = 1;
            if ($orientation instanceof PvPanelOrientation && $angle > 0) {
                $yield = KeyFigures::getYield($orientation, $angle);
                // \Log::debug('Heater: Yield for '.$orientation->name.' at '.$angle.' degrees = '.$yield->yield);
                if ($yield instanceof PvPanelYield) {
                    //TODO: Belgium has no location factors (yet?). For now we return the yield, as most coast regions
                    // have a location factor of 1, which is alike the average solar yield in Belgium
                    $locationFactor = KeyFigures::getLocationFactor($this->building->postal_code, $this->building->user->cooperation->country);
                    // \Log::debug('Heater: Location factor for '.$this->building->postal_code.' is '.$locationFactor?->factor);
                    $helpFactor = $locationFactor instanceof PvPanelLocationFactor
                        ? $yield->yield * $locationFactor->factor
                        : $yield->yield;
                }
            }
            // \Log::debug('Heater: helpfactor: '.$helpFactor);

            $systemSpecs = KeyFigures::getSystemSpecifications($result['consumption']['water'], $helpFactor);

            if (is_array($systemSpecs) && array_key_exists('boiler', $systemSpecs) && array_key_exists('collector',
                    $systemSpecs)) {
                $result['specs'] = [
                    'size_boiler' => $systemSpecs['boiler'],
                    'size_collector' => $systemSpecs['collector'],
                ];

                // \Log::debug('Heater: For this water consumption you need this heater: '.json_encode($systemSpecs));
                $result['production_heat'] = $systemSpecs['production_heat'];
                $result['savings_gas'] = $result['production_heat'] / Kengetallen::gasKwhPerM3();
                $result['percentage_consumption'] = data_get($result, 'consumption.gas', 0) > 0 ? ($result['savings_gas'] / $result['consumption']['gas']) * 100 : 0;
                $result['savings_co2'] = RawCalculator::calculateCo2Savings($result['savings_gas']);
                $result['savings_money'] = round(app(CalculatorService::class)
                    ->forBuilding($this->building)
                    ->calculateMoneySavings($result['savings_gas']));

                $componentCostBoiler = HeaterComponentCost::where('component', 'boiler')->where('size',
                    $result['specs']['size_boiler'])->first();
                $componentCostCollector = HeaterComponentCost::where('component', 'collector')->where('size',
                    $result['specs']['size_collector'])->first();
                $result['cost_indication'] = $componentCostBoiler->cost + $componentCostCollector->cost;

                $result['interest_comparable'] = number_format(BankInterestCalculator::getComparableInterest($result['cost_indication'],
                    $result['savings_money']), 1);


                $answer = array_merge($this->getAnswer('heat-source'), $this->getAnswer('heat-source-warm-tap-water'));

                $currentYear = Carbon::now()->year;
                $result['year'] = in_array('sun-boiler', $answer) ? $currentYear + 5 : $currentYear;

                if ($helpFactor >= 0.84) {
                    $result['performance'] = [
                        'alert' => 'green',
                        'text' => __('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.ideal'),
                    ];
                } elseif ($helpFactor < 0.70) {
                    $result['performance'] = [
                        'alert' => 'red',
                        'text' => __('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.no-go'),
                    ];
                } else {
                    $result['performance'] = [
                        'alert' => 'yellow',
                        'text' => __('woningdossier.cooperation.tool.solar-panels.indication-for-costs.performance.possible'),
                    ];
                }
            }
        }

        return $result;
    }
}
