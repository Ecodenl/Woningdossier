<?php

namespace App\Calculations;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\RawCalculator;
use App\Helpers\HighEfficiencyBoilerCalculator;
use App\Helpers\Kengetallen;
use App\Helpers\Number;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingService;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Models\UserEnergyHabit;
use App\Services\CalculatorService;
use Illuminate\Support\Arr;

class Ventilation
{
    const string NATURAL = 'natural';
    const string MECHANICAL = 'mechanical';
    const string BALANCED = 'balanced';
    const string DECENTRAL = 'decentral';

    public static function getTypes(): array
    {
        return [
            1 => self::NATURAL,
            2 => self::MECHANICAL,
            3 => self::BALANCED,
            4 => self::DECENTRAL,
        ];
    }

    /**
     * Calculate the wall insulation costs and savings etc.
     */
    public static function calculate(
        Building $building,
        InputSource $inputSource,
        ?UserEnergyHabit $energyHabit,
        array $calculateData
    ): array
    {
        $step = Step::findByShort('ventilation');

        /** @var BuildingService|null $buildingVentilationService */
        $buildingVentilationService = $building->getBuildingService('house-ventilation', $inputSource);

        $improvement = '';
        $remark = '';
        $considerables = [];
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

            $ventilationTypes = self::getTypes();

            $ventilationType = $ventilationTypes[$buildingVentilation->calculate_value];

            // Get all measures, will be conditionally unset
            $measures = [
                'ventilation-balanced-wtw',
                'ventilation-decentral-wtw',
                'ventilation-demand-driven',
                'crack-sealing',
            ];
            $measures = array_flip($measures);

            // If "No crack sealing" is NOT checked AND crack sealing element calculate value is 2, 3 or 4 ( >= 2..)
            // Crack sealing measure should be added.
            // As it's added on beforehand, it should be removed if:
            // "no crack sealing" is checked OR crack sealing element calculate value is 1 ( < 2)
            // because: either there is no crack sealing or it's all okay
            $currentCrackSealingCalculateValue = $currentCrackSealing->elementValue->calculate_value ?? 10;

            // now check al the conditions for the measures.
            switch ($ventilationType) {
                case self::NATURAL:
                    // "different" type which returns early
                    unset($measures['crack-sealing']);

                    $improvement = 'Natuurlijke ventilatie is niet zo goed voor het comfort en zorgt voor een hoog energiegebruik. Daarom worden de huizen steeds luchtdichter gemaakt en van goede isolatie voorzien. Om een gezond binnenklimaat te bereiken is hierbij een andere vorm van ventilatie nodig. De volgende opties kunt u overwegen:';
                    $remark = __('cooperation/tool/ventilation.calculations.warning');
                    break;

                case self::MECHANICAL:
                    if ($currentlyDemandDriven) {
                        // if the ventilation is already demand driven, remove that advice
                        unset($measures['ventilation-demand-driven']);
                    }

                    if (in_array('none', Arr::get(
                        $calculateData,
                        'building_ventilations.how'
                    ) ?? []) || $currentCrackSealingCalculateValue < 2) {
                        unset($measures['crack-sealing']);
                    }

                    $improvement = 'Oude ventilatoren gebruiken soms nog wisselstroom en verbruiken voor dezelfde prestatie veel meer elektriciteit en maken meer geluid dan moderne gelijkstroom ventilatoren. De besparing op de gebruikte stroom kan oplopen tot ca. 80 %. Een installateur kan direct beoordelen of u nog een wisselstroom ventilator heeft.';
                    $remark = __('cooperation/tool/ventilation.calculations.warning');
                    break;

                case self::BALANCED:
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

                    if (in_array('none', Arr::get(
                        $calculateData,
                        'building_ventilations.how'
                    ) ?? []) || $currentCrackSealingCalculateValue < 2) {
                        unset($measures['crack-sealing']);
                    }

                    $improvement = 'Uw woning is voorzien van een energiezuinig en duurzaam ventilatiesysteem. Zorg voor goed onderhoud en goed gebruik zo dat de luchtkwaliteit in de woning optimaal blijft.';
                    $remark = __('cooperation/tool/ventilation.calculations.warning');
                    break;

                case self::DECENTRAL:
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

                    if (in_array('none', Arr::get(
                        $calculateData,
                        'building_ventilations.how'
                    ) ?? []) || $currentCrackSealingCalculateValue < 2) {
                        unset($measures['crack-sealing']);
                    }

                    $improvement = 'Uw woning is voorzien van een energiezuinig en duurzaam ventilatiesysteem. Zorg voor goed onderhoud en goed gebruik zo dat de luchtkwaliteit in de woning optimaal blijft.';
                    $remark = __('cooperation/tool/ventilation.calculations.warning');
                    break;
            }

            if (array_key_exists('crack-sealing', $measures)) {
                // Crack sealing gives a percentage of savings. This is dependent on the application (place or replace)
                // and the gas usage (for heating)

                $result['crack_sealing']['cost_indication'] = 0;
                $result['crack_sealing']['savings_gas'] = 0;

                // (we know that calculate_value is > 1, but for historic logic reasons..
                if ($currentCrackSealing instanceof BuildingElement && $currentCrackSealingCalculateValue > 1) {
                    $gas = 0;
                    if ($energyHabit instanceof UserEnergyHabit) {
                        $usages = HighEfficiencyBoilerCalculator::init($building, $inputSource)->calculateGasUsage();
                        $gas = $usages['heating']['bruto'];
                    }

                    if (2 == $currentCrackSealingCalculateValue) {
                        $gasSaving = (Kengetallen::PERCENTAGE_GAS_SAVINGS_REPLACE_CRACK_SEALING / 100) * $gas;
                    } else {
                        $gasSaving = (Kengetallen::PERCENTAGE_GAS_SAVINGS_PLACE_CRACK_SEALING / 100) * $gas;
                    }

                    // we dont want negative results
                    $result['crack_sealing']['savings_gas'] = Number::isNegative($gasSaving) ? 0 : $gasSaving;

                    /** @var MeasureApplication $measureApplication */
                    $measureApplication = MeasureApplication::where(
                        'short',
                        'crack-sealing'
                    )->first();

                    $result['crack_sealing']['cost_indication'] = RawCalculator::calculateMeasureApplicationCosts(
                        $measureApplication,
                        1,
                        null,
                        false
                    );
                    $result['crack_sealing']['savings_co2'] = RawCalculator::calculateCo2Savings($result['crack_sealing']['savings_gas']);

                    $result['crack_sealing']['savings_money'] = app(CalculatorService::class)
                        ->forBuilding($building)
                        ->calculateMoneySavings($result['crack_sealing']['savings_gas']);

                    $result['crack_sealing']['interest_comparable'] = number_format(BankInterestCalculator::getComparableInterest(
                        $result['crack_sealing']['cost_indication'],
                        $result['crack_sealing']['savings_money']
                    ), 1);
                }
            }
            // al conditions have been checked, we can safely get the measures
            $measureApplications = MeasureApplication::where('step_id', '=', $step->id)
                ->whereIn('short', array_keys($measures))->get();

            $considerables = [];

            foreach ($measureApplications as $measureApplication) {
                $considerables[$measureApplication->id] = [
                    'is_considerable' => $building->user->considers($measureApplication, $inputSource),
                    'name' => $measureApplication->measure_name
                ];
            }

            if (count($considerables) > 0 && 'natural' !== $ventilationType) {
                $improvement .= '  Om de ventilatie verder te verbeteren kunt u de volgende opties overwegen:';
            }
        }

        return compact('improvement', 'considerables', 'remark', 'result');
    }
}
