<?php

namespace App\Calculations;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\HighEfficiencyBoilerCalculator;
use App\Helpers\Kengetallen;
use App\Helpers\KeyFigures\Heater\KeyFigures;
use App\Models\Building;
use App\Models\ComfortLevelTapWater;
use App\Models\InputSource;
use App\Models\ServiceValue;
use App\Models\ToolQuestion;
use App\Models\UserEnergyHabit;

class HeatPump
{

    /**
     * @var Building
     */
    protected $building;
    /**
     * @var ServiceValue
     */
    protected $heatPump;
    /**
     * @var int
     */
    protected $desiredPower = 0;
    /**
     * @var int
     */
    protected $requiredPower = 0;

    protected $advices = [];

    public function __construct(Building $building, ServiceValue $heatPump, int $desiredPower)
    {
        $this->building     = $building;
        $this->heatPump = $heatPump;
        $this->desiredPower = $desiredPower;
    }

    public static function calculate(Building $building, ServiceValue $heatPump, int $desiredPower)
    {
        $calculator = new static($building, $heatPump, $desiredPower);

        return $calculator->performCalculations();
    }

    public function performCalculations()
    {
        // First calculate the power of the heat pump (advised system)
        $this->requiredPower = $this->calculateAdvisedSystemRequiredPower();
        // lookup the characteristics of the chosen heat pump (tool question answer).
        $characteristics     = $this->lookupHeatpumpCharacteristics($this->heatPump);

        // $characteristics->type = 'hybrid' / 'full'

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

        $masterInputSource = InputSource::findByShort(
            InputSource::MASTER_SHORT
        );

        // D2
        $amountGas = $this->building->getAnswer(
            $masterInputSource,
            ToolQuestion::findByShort('amount-gas')
        );

        $gasUsage = HighEfficiencyBoilerCalculator::calculateGasUsage(
            $boiler,
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
        if ($characteristics->type === 'full') {
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
            ));
        // C73 = from mapping Maatregelopties en kengetallen: B58:D60 icm future situation
        $electricityUsageCooking = 0;
        // D11
        $currentElectricityUsage = $this->building->getAnswer(
            $masterInputSource,
            ToolQuestion::findByShort('amount-electricity')
        );
        // D12
        $currentElectricityUsageHeating = 0;
        // D13
        $currentElectricityUsageTapWater = 0;
        // D14 = from mapping Maatregelopties en kengetallen B58:D60 icm current situation
        $currentElectricityUsageCooking =

            // these values aren't part of the outcome.

            // if volledige warmtepomp: D2
            // else: D2 - (C68+C69+C70)
        $savingsGas = $amountGas;
        if ($characteristics->type !== 'full') {
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

        $result['interest_comparable'] = number_format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);


        return $result;
    }

    public function calculateAdvisedSystemRequiredPower()
    {
        // todo mapping table maken (kengetallen B88:D118)
        $wattPerSquareMeter = $kengetallen->getWperM2(
            $this->isolationScore()
        );

        $masterInputSource = InputSource::findByShort(
            InputSource::MASTER_SHORT
        );
        $surface           = $this->building->getAnswer(
            $masterInputSource,
            ToolQuestion::findByShort('surface')
        );

        return ($wattPerSquareMeter * $surface) / 1000;
    }

    public function calculateShareHeating()
    {
        // todo mapping table maken met from en to column (coverage rate, kengetallen C121:F131)
        // where betafactor < to and >= from

        return 0;
    }

    public function lookupHeatpumpCharacteristics()
    {
        // todo create heat pump characteristics table (berekeningen B39:I57)


    }

    public function betafactor()
    {
        return number_format(
            $this->desiredPower / max($this->requiredPower, 1),
            1,
            '.',
            ''
        );
    }

    public function isolationScore()
    {
        // todo: advices if one or more factors are <= 1
        // set advices here
        //

        return $factorGlassLivingArea * 1.5 +
               $factorGlassSleepingArea * 0.5 +
               $factorStateWallInsulation +
               $factorStateFloorInsulation +
               $factorStateRoofInsulation;
    }
}