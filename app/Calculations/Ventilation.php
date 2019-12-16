<?php

namespace App\Calculations;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\HighEfficiencyBoilerCalculator;
use App\Helpers\Kengetallen;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingService;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Models\UserEnergyHabit;
use Illuminate\Support\Arr;

class Ventilation
{

    /**
     * Calculate the wall insulation costs and savings etc.
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param UserEnergyHabit|null $energyHabit
     * @param array $calculateData
     *
     * @return array;
     */
    public static function calculate(Building $building, InputSource $inputSource, $energyHabit, array $calculateData): array
    {
        $step = Step::where('slug', '=', 'ventilation')->first();

        /** @var BuildingService $buildingVentilationService */
        $buildingVentilationService = $building->getBuildingService('house-ventilation', $inputSource);

        $improvement = '';
        $advices = null;
        $remark = '';
        $result = [
            'crack_sealing' => [
                'savings_co2' => null,
                'savings_gas' => null,
                'savings_money' => null,
                'cost_indication' => null,
                'interest_comparable' => null,
            ],
        ];

        if ($buildingVentilationService instanceof BuildingService) {

            /** @var ServiceValue $buildingVentilation */
            $buildingVentilation = $buildingVentilationService->serviceValue;

            $currentCrackSealing = $building->getBuildingElement('crack-sealing', $inputSource);

            $currentlyDemandDriven = $buildingVentilationService->extra['demand_driven'] ?? false;
            $currentlyHeatRecovery = $buildingVentilationService->extra['heat_recovery'] ?? false;

            $ventilationTypes = [
                1 => 'natural',
                2 => 'mechanic',
                3 => 'balanced',
                4 => 'decentral',
            ];

            $ventilationType = $ventilationTypes[$buildingVentilation->calculate_value];

            // Get all measures, will be conditionally unset
            $measures = [
                'ventilation-balanced-wtw',
                'ventilation-decentral-wtw',
                'ventilation-demand-driven',
                'crack-sealing',
            ];
            $measures = array_flip($measures);

            if ($ventilationType === 'natural') {
                // "different" type which returns early
                unset($measures['crack-sealing']);

                $improvement = 'Natuurlijke ventilatie is  niet zo goed voor het comfort en zorgt voor een hoog energiegebruik. Daarom worden de huizen steeds luchtdichter gemaakt en van goede isolatie voorzien. Om een gezond binnenklimaat te bereiken is hierbij een andere vorm van ventilatie nodig. De volgende opties kunt u overwegen:';
                $remark = __('my-plan.warnings.ventilation');
            }
            if ($ventilationType === 'mechanic') {

                if ($currentlyDemandDriven) {
                    // if the ventilation is already demand driven, remove that advice
                    unset($measures['ventilation-demand-driven']);
                }

                // If "No crack sealing" is NOT checked AND crack sealing element calculate value is 2, 3 or 4 ( >= 2..)
                // Crack sealing measure should be added.
                // As it's added on beforehand, it should be removed if:
                // "no crack sealing" is checked OR crack sealing element calculate value is 1 ( < 2)
                // because: either there is no crack sealing or it's all okay
                $currentCrackSealingCalculateValue = $currentCrackSealing->elementValue->calculate_value ?? 10;

                if (in_array('none', Arr::get($calculateData, 'building_ventilations.how') ?? []) || $currentCrackSealingCalculateValue < 2) {
                    unset($measures['crack-sealing']);
                }

                $improvement = 'Oude ventilatoren gebruiken soms nog wisselstroom en verbruiken voor dezelfde prestatie veel meer elektriciteit en maken meer geluid dan moderne gelijkstroom ventilatoren. De besparing op de gebruikte stroom kan oplopen tot ca. 80 %. Een installateur kan direct beoordelen of u nog een wisselstroom ventilator heeft.';
                $remark = __('my-plan.warnings.ventilation');
            }
            if ($ventilationType === 'balanced') {

                // always unset
                unset($measures['ventilation-decentral-wtw']);

                // if the ventilation already has heat recovery, remove that advice
                if ($currentlyHeatRecovery) {
                    unset($measures['ventilation-balanced-wtw']);
                }

                // if the ventilation is already demand driven, remove that advice
                if ($currentlyDemandDriven) {
                    unset($measures['ventilation-demand-driven']);
                }

                // If "No crack sealing" is NOT checked AND crack sealing element calculate value is 2, 3 or 4 ( >= 2..)
                // Crack sealing measure should be added.
                // As it's added on beforehand, it should be removed if:
                // "no crack sealing" is checked OR crack sealing element calculate value is 1 ( < 2)
                // because: either there is no crack sealing or it's all okay
                $currentCrackSealingCalculateValue = $currentCrackSealing->elementValue->calculate_value ?? 10;

                if (in_array('none', Arr::get($calculateData, 'building_ventilations.how') ?? []) || $currentCrackSealingCalculateValue < 2) {
                    unset($measures['crack-sealing']);
                }

                $improvement = 'Uw woning is voorzien van een energiezuinig en duurzaam ventilatiesysteem. Zorg voor goed onderhoud en goed gebruik zo dat de luchtkwaliteit in de woning optimaal blijft.';
                $remark = __('my-plan.warnings.ventilation');
            }
            if ($ventilationType === 'decentral') {

                // always unset
                unset($measures['ventilation-balanced-wtw']);

                // if the ventilation already has heat recovery, remove that advice
                if ($currentlyHeatRecovery) {
                    unset($measures['ventilation-decentral-wtw']);
                }

                // if the ventilation is already demand driven, remove that advice
                if ($currentlyDemandDriven) {
                    unset($measures['ventilation-demand-driven']);
                }

                // If "No crack sealing" is NOT checked AND crack sealing element calculate value is 2, 3 or 4 ( >= 2..)
                // Crack sealing measure should be added.
                // As it's added on beforehand, it should be removed if:
                // "no crack sealing" is checked OR crack sealing element calculate value is 1 ( < 2)
                // because: either there is no crack sealing or it's all okay
                $currentCrackSealingCalculateValue = $currentCrackSealing->elementValue->calculate_value ?? 10;

                if (in_array('none', Arr::get($calculateData, 'building_ventilations.how') ?? []) || $currentCrackSealingCalculateValue < 2) {
                    unset($measures['crack-sealing']);
                }

                $improvement = 'Uw woning is voorzien van een energiezuinig en duurzaam ventilatiesysteem. Zorg voor goed onderhoud en goed gebruik zo dat de luchtkwaliteit in de woning optimaal blijft.';
                $remark = __('my-plan.warnings.ventilation');
            }

            $advices = MeasureApplication::where('step_id', '=', $step->id)
                ->whereIn('short', array_keys($measures))->get();

            $advices->each(function ($advice) {
                $advice->name = $advice->measure_name;
            });

            if (array_key_exists('crack-sealing', $measures)) {
                // Crack sealing gives a percentage of savings. This is dependent on the application (place or replace)
                // and the gas usage (for heating)


                $result['crack_sealing']['cost_indication'] = 0;
                $result['crack_sealing']['savings_gas'] = 0;

                // (we know that calculate_value is > 1, but for historic logic reasons..
                if ($currentCrackSealing instanceof BuildingElement && $currentCrackSealingCalculateValue > 1) {
                    $gas = 0;
                    if ($energyHabit instanceof UserEnergyHabit) {
                        $boiler = $building->getServiceValue('boiler', $inputSource);
                        $usages = HighEfficiencyBoilerCalculator::calculateGasUsage($boiler, $energyHabit);
                        $gas = $usages['heating']['bruto'];
                    }

                    if (2 == $currentCrackSealingCalculateValue) {
                        $gasSaving = (Kengetallen::PERCENTAGE_GAS_SAVINGS_REPLACE_CRACK_SEALING / 100) * $gas;
                    } else {
                        $gasSaving = (Kengetallen::PERCENTAGE_GAS_SAVINGS_PLACE_CRACK_SEALING / 100) * $gas;

                    }

                    // we dont want negative results
                    $result['crack_sealing']['savings_gas'] = $gasSaving < 0 ? 0 : $gasSaving;

                    /** @var MeasureApplication $measureApplication */
                    $measureApplication = MeasureApplication::where('short',
                        'crack-sealing')->first();

                    $result['crack_sealing']['cost_indication'] = Calculator::calculateMeasureApplicationCosts($measureApplication,
                        1, null, false);
                    $result['crack_sealing']['savings_co2'] = Calculator::calculateCo2Savings($result['crack_sealing']['savings_gas']);
                    $result['crack_sealing']['savings_money'] = Calculator::calculateMoneySavings($result['crack_sealing']['savings_gas']);
                    $result['crack_sealing']['interest_comparable'] = number_format(BankInterestCalculator::getComparableInterest($result['crack_sealing']['cost_indication'],
                        $result['crack_sealing']['savings_money']), 1);
                }
            }

            // Add interest (or not)
            // This is needed for the javascript
            $buildingOwner = $building->user;

            foreach ($advices as $advice) {
                if ($buildingOwner->hasInterestIn($advice, $inputSource)) {
                    $advice->interest = true;
                }
            }

            if (count($advices) > 0 && $ventilationType !== 'natural') {
                $improvement .= '  Om de ventilatie verder te verbeteren kunt u de volgende opties overwegen:';
            }

        }
        return compact('improvement', 'advices', 'remark', 'result');
    }
}