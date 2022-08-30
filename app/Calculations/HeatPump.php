<?php

namespace App\Calculations;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\HighEfficiencyBoilerCalculator;
use App\Helpers\Kengetallen;
use App\Helpers\KeyFigures\Heater\KeyFigures;
use App\Models\Building;
use App\Models\ElementValue;
use App\Models\HeatPumpCharacteristic;
use App\Models\InputSource;
use App\Models\KeyFigureHeatPumpCoverage;
use App\Models\KeyFigureInsulationFactor;
use App\Models\ServiceValue;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;

class HeatPump
{

    /**
     * @var Building
     */
    protected $building;
    /**
     * @var
     */
    protected $boiler;
    /**
     * "Hoge temperatuur", "50 graden", "Lage temperatuur"
     * @var ToolQuestionCustomValue
     */
    protected $heatingTemperature;
    /**
     * @var ServiceValue|ToolQuestionCustomValue
     */
    protected $heatPumpConfigurable;
    /**
     * @var int
     */
    protected $desiredPower = 0;
    /**
     * @var int
     */
    protected $requiredPower = 0;

    protected $advices = [];

    /**
     * @var InputSource
     */
    protected $inputSource;

    /**
     * @param  Building  $building
     * @param  ServiceValue|ToolQuestionCustomValue  $heatPumpConfigurable
     * @param  int  $desiredPower
     */
    public function __construct(
        Building $building,
        $boiler,
        ToolQuestionCustomValue $heatingTemperature,
        $heatPumpConfigurable,
        int $desiredPower
    ) {
        $this->building             = $building;
        $this->boiler               = $boiler;
        $this->heatingTemperature   = $heatingTemperature;
        $this->heatPumpConfigurable = $heatPumpConfigurable;
        $this->desiredPower         = $desiredPower;

        $this->inputSource = InputSource::findByShort(
            InputSource::MASTER_SHORT
        );
    }

    /**
     * @param  Building  $building
     * @param  ServiceValue|ToolQuestionCustomValue  $heatPumpConfigurable
     * @param  int  $desiredPower
     *
     * @return array
     */
    public static function calculate(
        Building $building,
        $boiler,
        ToolQuestionCustomValue $heatingTemperature,
        $heatPumpConfigurable,
        int $desiredPower
    ) {
        $calculator = new static(
            $building,
            $boiler,
            $heatingTemperature,
            $heatPumpConfigurable,
            $desiredPower
        );

        return $calculator->performCalculations();
    }

    public function performCalculations()
    {
        // First calculate the power of the heat pump (advised system)
        $this->requiredPower = $this->calculateAdvisedSystemRequiredPower();
        // lookup the characteristics of the chosen heat pump (tool question answer).
        $characteristics = $this->lookupHeatPumpCharacteristics();

        // note what this will return: either 40% or 0.4 ??
        $shareHeating = $this->calculateShareHeating();
        // return value affects other calculations.

        $advisedSystem = [
            'required_power'  => $this->requiredPower,
            // C60
            'desired_power'   => $this->desiredPower,
            // todo filled by user. C61
            'share_heating'   => $shareHeating,
            // C62
            'share_tap_water' => $characteristics->share_wtw,
            // C63
            'scop_heating'    => $characteristics->scop_heating,
            // C64
            'scop_tap_water'  => $characteristics->scop_wtw,
            // C65
        ];

        // D2
        $amountGas = $this->building->getAnswer(
            $this->inputSource,
            ToolQuestion::findByShort('amount-gas')
        );

        $gasUsage = HighEfficiencyBoilerCalculator::calculateGasUsage(
            $this->boiler,
            $this->building->user->energyHabit,
            $amountGas
        );

        // D8
        $nettoGasUsageHeating = data_get($gasUsage, 'heating.netto', 0);
        // D9
        $nettoGasUsageTapWater = data_get($gasUsage, 'tap_water.netto', 0);

        // Now we can calculate the new energy usage
        // C68 = D8 - (D8 * C62)
        $gasUsageHeating = $nettoGasUsageHeating - ($nettoGasUsageHeating * $advisedSystem['share_heating']);
        // C69 = D9 - (D9 * C63)
        $gasUsageTapWater = $nettoGasUsageTapWater - ($nettoGasUsageTapWater * $advisedSystem['share_tap_water']);
        // C70
        $gasUsageCooking = data_get($gasUsage, 'cooking', 0);

        // E71
        // if volledige warmtepomp: C68 * KeyFigures::M3_GAS_TO_KWH
        // else: 0
        $electricalReheating = 0;
        if ($characteristics->type === HeatPumpCharacteristic::TYPE_FULL) {
            $electricalReheating = $gasUsageHeating * KeyFigures::M3_GAS_TO_KWH;
        }

        // C71 = (((D8-C68) * KeyFigures::M3_GAS_TO_KWH) / scop_heating) + E71
        $electricityUsageHeating = ((($nettoGasUsageHeating - $gasUsageHeating) * KeyFigures::M3_GAS_TO_KWH) / max(
                    $advisedSystem['scop_heating'],
                    1
                )) + $electricalReheating;
        // C72 = ((D9-C69) * KeyFigures::M3_GAS_TO_KWH) / scop_tap_water)
        $electricityUsageTapWater = (($nettoGasUsageTapWater - $gasUsageTapWater) * KeyFigures::M3_GAS_TO_KWH) / max(
                $advisedSystem['scop_tap_water'],
                1
            );
        // C73 = from mapping Maatregelopties en kengetallen: B58:D60 icm future situation
        $electricityUsageCooking = 0;
        // D11
        $currentElectricityUsage = $this->building->getAnswer(
            $this->inputSource,
            ToolQuestion::findByShort('amount-electricity')
        );
        // D12
        $currentElectricityUsageHeating = 0;
        // D13
        $currentElectricityUsageTapWater = 0;
        // D14 = from mapping Maatregelopties en kengetallen B58:D60 icm current situation
        // todo opzoeken: in codebase of database?
        $currentElectricityUsageCooking = $this->energyUsageForCooking();

        // these values aren't part of the outcome.

        // if volledige warmtepomp: D2
        // else: D2 - (C68+C69+C70)
        $savingsGas = $amountGas;
        if ($characteristics->type !== HeatPumpCharacteristic::TYPE_FULL) {
            $savingsGas = $amountGas - ($gasUsageHeating + $gasUsageTapWater + $gasUsageCooking);
        }

        // (C71+C72+C73) - (D12-D13-D14)
        $extraConsumptionElectricity = ($electricityUsageHeating +
                                        $electricityUsageTapWater +
                                        $electricityUsageCooking) -
                                       $currentElectricityUsageHeating -
                                       $currentElectricityUsageTapWater -
                                       $currentElectricityUsageCooking;

        $savingsCo2 = Calculator::calculateCo2Savings($savingsGas) -
                      ($extraConsumptionElectricity * Kengetallen::CO2_SAVINGS_ELECTRICITY);

        $savingsMoney = Calculator::calculateMoneySavings($savingsGas) -
                        ($extraConsumptionElectricity * Kengetallen::EURO_SAVINGS_ELECTRICITY);

        $result = [
            'savings_gas'                   => $savingsGas,
            'extra_consumption_electricity' => $extraConsumptionElectricity,
            'savings_co2'                   => $savingsCo2,
            'savings_money'                 => $savingsMoney,
            'cost_indication'               => $characteristics->costs,
            'advised_system'                => $advisedSystem,
            'advices'                       => $this->advices,
        ];

        $result['interest_comparable'] = number_format(
            BankInterestCalculator::getComparableInterest(
                $result['cost_indication'],
                $result['savings_money']
            ),
            1
        );

        return $result;
    }

    public function calculateAdvisedSystemRequiredPower()
    {
        $kfInsulationFactor = KeyFigureInsulationFactor::forInsulationFactor(
            $this->insulationScore()
        )->first();
        $wattPerSquareMeter = $kfInsulationFactor->energy_consumption_per_m2 ?? 140;

        $surface = $this->building->getAnswer(
            $this->inputSource,
            ToolQuestion::findByShort('surface')
        );

        return ($wattPerSquareMeter * $surface) / 1000;
    }

    public function calculateShareHeating(): int
    {
        $coverage = KeyFigureHeatPumpCoverage::forBetaFactor(
            $this->betafactor()
        )->
        forHeatingTemperature($this->heatingTemperature)
                                             ->first();

        return $coverage->percentage ?? 0;
    }

    public function lookupHeatPumpCharacteristics(): ?HeatPumpCharacteristic
    {
        return HeatPumpCharacteristic::forHeatPumpConfigurable(
            $this->heatPumpConfigurable
        )
                                     ->forHeatingTemperature(
                                         $this->heatingTemperature
                                     )
                                     ->first();
    }

    // = C61
    public function betafactor()
    {
        return number_format(
            $this->desiredPower / max($this->requiredPower, 1),
            1,
            '.',
            ''
        );
    }

    protected function energyUsageForCooking()
    {
        $cookType = $this->building->getAnswer(
            $this->inputSource,
            ToolQuestion::findByShort('cook-type')
        );

        switch ($cookType) {
            case 'gas':
                return Kengetallen::ENERGY_USAGE_COOK_TYPE_GAS;
            case 'electric':
                return Kengetallen::ENERGY_USAGE_COOK_TYPE_ELECTRIC;
            case 'induction':
                return Kengetallen::ENERGY_USAGE_COOK_TYPE_INDUCTION;
        }

        return Kengetallen::ENERGY_USAGE_COOK_TYPE_GAS;
    }

    public function insulationScore()
    {
        // todo: advices if one or more factors are <= 1
        // set advices here
        //
        $toolQuestions = [
            'current-living-rooms-windows'   => 1.5,
            'current-sleeping-rooms-windows' => 0.5,
            'current-wall-insulation'        => 1,
            'current-floor-insulation'       => 1,
            'current-roof-insulation'        => 1,
        ];

        $score = 0;

        foreach ($toolQuestions as $toolQuestion => $weight) {
            /** @var ElementValue $elementValue */
            $elementValue = $this->building->getAnswer(
                $this->inputSource,
                ToolQuestion::findByShort($toolQuestion)
            );
            $factor       = (int)$elementValue->insulation_factor;
            if ($factor <= 1) {
                // todo check how to pass this when errors / notifications implementation is in place.
                $this->advices[$toolQuestion] = $toolQuestion;
            }
            $score += ($factor * $weight);
        }

        return $score;
    }

    public function getAdvices(): array
    {
        return $this->advices;
    }
}