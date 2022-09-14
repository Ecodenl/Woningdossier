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
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use App\Models\UserEnergyHabit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class HeatPump extends \App\Calculations\Calculator
{
    /**
     * Answer for tool question 'new-boiler-type'
     *
     * @var \App\Models\ServiceValue|null
     */
    protected ?ServiceValue $boiler;

    /**
     * Answer for tool question 'new-boiler-setting-comfort-heat'
     * ("Hoge temperatuur", "50 graden", "Lage temperatuur")
     *
     * @var \App\Models\ToolQuestionCustomValue|null
     */
    protected ?ToolQuestionCustomValue $heatingTemperature;

    /**
     * Answer for tool question 'new-heat-pump-type' OR 'new-building-heating-application'  // TODO: Check this one
     *
     * @var ServiceValue|ToolQuestionCustomValue|null
     */
    protected $heatPumpConfigurable;

    /**
     * Answer for tool question 'heat-pump-preferred-power'
     *
     * @var int
     */
    protected int $desiredPower = 0;

    protected int $requiredPower = 0;

    protected array $advices = [];

    protected UserEnergyHabit $energyHabit;

    /**
     * @param  \App\Models\Building  $building
     * @param  \App\Models\InputSource  $inputSource
     * @param  \App\Models\UserEnergyHabit  $energyHabit
     * @param  \Illuminate\Support\Collection|null  $answers
     */
    public function __construct(
        Building $building,
        InputSource $inputSource,
        UserEnergyHabit $energyHabit,
        ?Collection $answers = null
    )
    {
        $this->building = $building;
        $this->inputSource = $inputSource;
        $this->energyHabit = $energyHabit;
        $this->answers = $answers;

        // TODO: Check if we can potentially move these inline so we only have to query when we actually need them
        $this->boiler = Service::findByShort('heat-pump')->values()
            ->where(
                'calculate_value',
                ToolQuestion::findByShort('new-boiler-type')->toolQuestionCustomValues()
                    ->whereShort($this->getAnswer('new-boiler-type'))->first()->extra['calculate_value'] ?? null
            )->first();
        $this->heatingTemperature = ToolQuestion::findByShort('new-boiler-setting-comfort-heat')
            ->toolQuestionCustomValues()->whereShort($this->getAnswer('new-boiler-setting-comfort-heat'))->first();
        $this->heatPumpConfigurable = Service::findByShort('heat-pump')->values()
            ->where(
                'calculate_value',
                ToolQuestion::findByShort('new-heat-pump-type')->toolQuestionCustomValues()
                    ->whereShort($this->getAnswer('new-heat-pump-type'))->first()->extra['calculate_value'] ?? null
            )->first();
        $this->desiredPower = $this->getAnswer('heat-pump-preferred-power') ?? 0;
    }

    /**
     * Short hand syntax to quickly calculate.
     *
     * @param  \App\Models\Building  $building
     * @param  \App\Models\InputSource  $inputSource
     * @param  \App\Models\UserEnergyHabit  $energyHabit
     * @param  \Illuminate\Support\Collection|null  $answers
     *
     * @return array
     */
    public static function calculate(
        Building $building,
        InputSource $inputSource,
        UserEnergyHabit $energyHabit,
        ?Collection $answers= null
    ): array
    {
        $calculator = new static(
            $building,
            $inputSource,
            $energyHabit,
            $answers,
        );

        return $calculator->performCalculations();
    }

    public function performCalculations(): array
    {
        // First calculate the power of the heat pump (advised system)
        $this->requiredPower = $this->calculateAdvisedSystemRequiredPower();
        // lookup the characteristics of the chosen heat pump (tool question answer).
        $characteristics = $this->lookupHeatPumpCharacteristics();

        // note what this will return: either 40% or 0.4 ??
        $shareHeating = $this->calculateShareHeating();
        // return value affects other calculations.

        $advisedSystem = [
            'required_power' => $this->requiredPower, // C60
            'desired_power' => $this->desiredPower, // C61
            'share_heating' => $shareHeating, // C62
            'share_tap_water' => $characteristics->share_percentage_tap_water ?? 0, // C63
            'scop_heating' => $characteristics->scop ?? 0, // C64
            'scop_tap_water' => $characteristics->scop_tap_water ?? 0, // C65
        ];

        // D2
        // TODO: Should we fall back to energyHabit? It could be a different input source
        $amountGas = $this->getAnswer('amount-gas') ?? $this->energyHabit->amount_gas;

        $gasUsage = HighEfficiencyBoilerCalculator::calculateGasUsage(
            $this->boiler,
            $this->energyHabit,
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
        if (optional($characteristics)->type === HeatPumpCharacteristic::TYPE_FULL) {
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
        // TODO: Should we fall back to energyHabit? It could be a different input source
        $currentElectricityUsage = $this->getAnswer('amount-electricity') ?? $this->energyHabit->amount_electricity;
        // D12
        $currentElectricityUsageHeating = 0;
        // D13
        $currentElectricityUsageTapWater = 0;
        // D14 = from mapping Maatregelopties en kengetallen B58:D60 icm current situation
        $currentElectricityUsageCooking = $this->energyUsageForCooking();

        // these values aren't part of the outcome.

        // if volledige warmtepomp: D2
        // else: D2 - (C68+C69+C70)
        $savingsGas = $amountGas;
        if (optional($characteristics)->type !== HeatPumpCharacteristic::TYPE_FULL) {
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
            'amount_gas' => $amountGas,
            'amount_electricity' => $currentElectricityUsage,
            'savings_gas' => $savingsGas,
            'extra_consumption_electricity' => $extraConsumptionElectricity,
            'savings_co2' => $savingsCo2,
            'savings_money' => $savingsMoney,
            'cost_indication' => $characteristics->costs ?? 0,
            'advised_system' => $advisedSystem,
            'advices' => $this->advices,
        ];

        $result['interest_comparable'] = $this->format(
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
        $kfInsulationFactor = KeyFigureInsulationFactor::forInsulationFactor($this->insulationScore())->first();
        $wattPerSquareMeter = $kfInsulationFactor->energy_consumption_per_m2 ?? 140;

        $surface = $this->getAnswer('surface');

        return $this->format(($wattPerSquareMeter * $surface) / 1000);
    }

    public function calculateShareHeating(): int
    {
        if ($this->heatingTemperature instanceof ToolQuestionCustomValue) {
            $coverage = KeyFigureHeatPumpCoverage::forBetaFactor($this->betaFactor())
                ->forHeatingTemperature($this->heatingTemperature)
                ->first();

            return $coverage->percentage ?? 0;
        }

        return 0;
    }

    public function lookupHeatPumpCharacteristics(): ?HeatPumpCharacteristic
    {
        if ($this->heatPumpConfigurable instanceof Model && $this->heatingTemperature instanceof ToolQuestionCustomValue) {
            return HeatPumpCharacteristic::forHeatPumpConfigurable($this->heatPumpConfigurable)
                ->forHeatingTemperature($this->heatingTemperature)
                ->first();
        }

        return null;
    }

    // = C61
    public function betaFactor()
    {
        return $this->format($this->desiredPower / max($this->requiredPower, 1), 1);
    }

    protected function energyUsageForCooking()
    {
        $cookType = $this->getAnswer('cook-type');

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

    public function insulationScore(): float
    {
        $toolQuestions = [
            'current-living-rooms-windows' => 1.5,
            'current-sleeping-rooms-windows' => 0.5,
            'current-wall-insulation' => 1,
            'current-floor-insulation' => 1,
            'current-roof-insulation' => 1,
        ];

        $score = 0;

        foreach ($toolQuestions as $toolQuestion => $weight) {
            /** @var ElementValue $elementValue */
            $elementValue = ElementValue::find($this->getAnswer($toolQuestion));

            $factor = optional($elementValue)->insulation_factor ?? 1;
            if ($factor <= 1) {
                // If the state of this element is bad we want to advise the user to fix this first
                $this->advices[$toolQuestion] = $toolQuestion;
            }
            $score += ($factor * $weight);
        }

        return $this->format($score / count($toolQuestions));
    }

    public function getAdvices(): array
    {
        return $this->advices;
    }

    /**
     * Format consistent float. We don't need to cast, PHP is smart enough to convert it to a float if it's used in
     * math.
     *
     * @param $number
     * @param  int  $decimals
     *
     * @return string
     */
    private function format($number, int $decimals = 2)
    {
        return number_format($number, $decimals, '.', '');
    }
}