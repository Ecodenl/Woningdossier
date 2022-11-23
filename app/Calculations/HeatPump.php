<?php

namespace App\Calculations;

use App\Deprecation\ToolHelper;
use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

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
     * Answer for tool question 'heat-pump-preferred-power'
     *
     * @var int
     */
    protected int $desiredPower = 0;

    protected $requiredPower = 0;

    protected array $advices = [];

    /**
     * @param  \App\Models\Building  $building
     * @param  \App\Models\InputSource  $inputSource
     * @param  \Illuminate\Support\Collection|null  $answers
     */
    public function __construct(Building $building, InputSource $inputSource, ?Collection $answers = null)
    {
        parent::__construct($building, $inputSource, $answers);

        $this->heatingTemperature = ToolQuestion::findByShort('new-boiler-setting-comfort-heat')
            ->toolQuestionCustomValues()->whereShort($this->getAnswer('new-boiler-setting-comfort-heat'))->first();
    }

    public function performCalculations(): array
    {
        // First calculate the power of the heat pump (advised system)
        $this->requiredPower = $this->calculateAdvisedSystemRequiredPower();
        // lookup the characteristics of the chosen heat pump (tool question answer).
        $characteristics = $this->lookupHeatPumpCharacteristics();

        $this->desiredPower = $this->getAnswer('heat-pump-preferred-power');
        // if it wasn't answered (by person in expert or example building)
        if ($this->desiredPower <= 0 && $characteristics instanceof HeatPumpCharacteristic) {
            if ($characteristics->type === HeatPumpCharacteristic::TYPE_FULL){
                // for full: required power
                $this->desiredPower = $this->requiredPower;
            }
            else {
                // for hybrid: fixed value / standard from table
                $this->desiredPower = $characteristics->standard_power_kw;
            }
        }

        // note what this will return: either 40% or 0.4 ??
        $shareHeating = $this->calculateShareHeating();
        // return value affects other calculations.

        $scopTw = 0;
        if ($characteristics instanceof HeatPumpCharacteristic){
            $scopTw = $characteristics->type == HeatPumpCharacteristic::TYPE_HYBRID ? 0 : ($characteristics->scop_tap_water ?? 0); // C65
        }

        $advisedSystem = [
            'required_power' => $this->requiredPower, // C60
            'desired_power' => $this->desiredPower, // C61
            'share_heating' => $shareHeating, // C62
            'share_tap_water' => $characteristics->share_percentage_tap_water ?? 0, // C63
            'scop_heating' => $characteristics->scop ?? 0, // C64
            // if hybrid: show scop_tap_water as 0 (asked by coaches)
            'scop_tap_water' => $scopTw,
        ];
        Log::debug(__METHOD__ . ' ' . json_encode($advisedSystem) . " (geadviseerd systeem)");

        // D2
        $amountGas = $this->getAnswer('amount-gas') ?? 0;
        Log::debug("D2: " . $amountGas . " (huidig gasverbruik)");

        // Get the boiler for the situation. Note if there is no boiler, the
        // user probably has a heat pump already, so we have to calculate with
        // the most efficient boiler.
//        $boiler = ToolHelper::getServiceValueByCustomValue('boiler', 'new-boiler-type',
//            $this->getAnswer('new-boiler-type'));
//        if (!$boiler instanceof ServiceValue){
//            // if boiler type was not filled in, we will calculate with the current boiler
//            $boiler = ToolHelper::getServiceValueByCustomValue('boiler', 'boiler-type',
//                $this->getAnswer('boiler-type'));
//        }
//        if (!$boiler instanceof ServiceValue) {
//            // if even the current boiler wasn't present, the user probably already
//            // has a heat pump, so we will calculate with the most efficient boiler
//            $boiler = Service::findByShort('boiler')->values()->orderByDesc('calculate_value')->limit(1)->first();
//        }

//        $gasUsage = HighEfficiencyBoilerCalculator::calculateGasUsage(
//            $boiler,
//            $this->energyHabit,
//            $amountGas
//        );
        // new
        Log::debug("=== Heating calculate ===");
        $energyUsage = Heating::calculate($this->building, $this->inputSource);
        Log::debug("=== Heating calculate done ===");

        // D8
        //$nettoGasUsageHeating = data_get($gasUsage, 'heating.netto', 0);
        // new
        $nettoGasUsageHeating = data_get($energyUsage, 'heating.current.gas', 0);
        Log::debug("D8: " . $nettoGasUsageHeating . " (netto gasverbruik verwarming)");
        // D9
        //$nettoGasUsageTapWater = data_get($gasUsage, 'tap_water.netto', 0);
        // new
        $nettoGasUsageTapWater = data_get($energyUsage, 'tap_water.current.gas', 0);
        Log::debug("D9: " . $nettoGasUsageTapWater . " (netto gasverbruik wtw)");

        // Now we can calculate the new energy usage
        // C68 = D8 - (D8 * C62)
        $gasUsageHeating = $nettoGasUsageHeating - ($nettoGasUsageHeating * ($advisedSystem['share_heating'] / 100));
        Log::debug("C68: " . $gasUsageHeating . " (gasverbruik verwarming)");
        // C69 = D9 - (D9 * C63)
        $gasUsageTapWater = $nettoGasUsageTapWater - ($nettoGasUsageTapWater * ($advisedSystem['share_tap_water'] / 100));
        Log::debug("C69: " . $gasUsageTapWater . " (gasverbruik wtw)");
        // C70
        // note cookingInSituation will be used later on as well
//        $cookingInSituation = ToolQuestion::findByShort('new-cook-type')
//                    ->toolQuestionCustomValues()
//                    ->whereShort($this->getAnswer('new-cook-type'))
//                    ->first();

        //$gasUsageCooking = optional($cookingInSituation)->short == 'gas' ? Kengetallen::ENERGY_USAGE_COOK_TYPE_GAS : 0;
        // new
        $gasUsageCooking = data_get($energyUsage, 'cooking.current.gas', 0);
        Log::debug("C70: " . $gasUsageCooking . " (gasverbruik koken)");

        // E71
        // if volledige warmtepomp: C68 * KeyFigures::M3_GAS_TO_KWH
        // else: 0
        $electricalReheating = 0;
        if (optional($characteristics)->type === HeatPumpCharacteristic::TYPE_FULL) {
            $electricalReheating = $gasUsageHeating * KeyFigures::M3_GAS_TO_KWH;
        }
        Log::debug("E71: " . $electricalReheating . " (elektrisch bijverwarmen)");

        // C71 = (((D8-C68) * KeyFigures::M3_GAS_TO_KWH) / scop_heating) + E71
        $electricityUsageHeating = ((($nettoGasUsageHeating - $gasUsageHeating) * KeyFigures::M3_GAS_TO_KWH) / max(
                    $advisedSystem['scop_heating'],
                    1
                )) + $electricalReheating;
        Log::debug("C71: " . $electricityUsageHeating . " (elektraverbruik verwarmen)");

        // C72 = ((D9-C69) * KeyFigures::M3_GAS_TO_KWH) / scop_tap_water)
        $electricityUsageTapWater = (($nettoGasUsageTapWater - $gasUsageTapWater) * KeyFigures::M3_GAS_TO_KWH) / max(
                $advisedSystem['scop_tap_water'],
                1
            );
        Log::debug("C72: " . $electricityUsageTapWater . " (elektraverbruik wtw)");

        // C73 = from mapping Maatregelopties en kengetallen: B58:D60 icm future situation
//        $electricityUsageCooking = 0;
//        if (optional($cookingInSituation)->short == 'electric') {
//            $electricityUsageCooking = Kengetallen::ENERGY_USAGE_COOK_TYPE_ELECTRIC;
//        }
//        if (optional($cookingInSituation)->short == 'induction') {
//            $electricityUsageCooking = Kengetallen::ENERGY_USAGE_COOK_TYPE_INDUCTION;
//        }
        // new
        $electricityUsageCooking = data_get($energyUsage, 'cooking.new.electricity');
        Log::debug("C73: " . $electricityUsageCooking . " (elektraverbruik koken)");

        // D11
        $currentElectricityUsage = $this->getAnswer('amount-electricity') ?? 0;
        Log::debug("D11: " . $currentElectricityUsage . " (huidig elektraverbruik)");
        // D12
        //$currentElectricityUsageHeating = 0;
        // new
        $currentElectricityUsageHeating = data_get($energyUsage, 'heating.current.electricity', 0);
        Log::debug("D12: " . $currentElectricityUsageHeating . " (huidig elektraverbruik verwarmen)");
        // D13
        //$currentElectricityUsageTapWater = 0;
        // new
        $currentElectricityUsageTapWater = data_get($energyUsage, 'tap_water.current.electricity', 0);
        Log::debug("D13: " . $currentElectricityUsageTapWater . " (huidig elektraverbruik wtw)");
        // D14 = from mapping Maatregelopties en kengetallen B58:D60 icm current situation
        //$currentElectricityUsageCooking = $this->energyUsageForCooking();
        // new
        $currentElectricityUsageCooking = data_get($energyUsage, 'cooking.current.electricity', 0);
        Log::debug("D14: " . $currentElectricityUsageCooking . " (huidig elektraverbruik koken)");

        // these values aren't part of the outcome.

        // if volledige warmtepomp: D2
        // else: D2 - (C68+C69+C70)
        $savingsGas = $amountGas;
        if (optional($characteristics)->type !== HeatPumpCharacteristic::TYPE_FULL) {
            Log::debug("C76: not full heatpump: savingsGas = " . $amountGas . ' - (' . $gasUsageHeating . ' + ' . $gasUsageTapWater . ' + ' . $gasUsageCooking . ')');
            $savingsGas = $amountGas - ($gasUsageHeating + $gasUsageTapWater + $gasUsageCooking);
        }
        Log::debug("C76: " . $savingsGas . " (gasbesparing)");

        // (C71+C72+C73) - (D12-D13-D14)
        Log::debug("C77: extraConsumptionElectricity = (" . $electricityUsageHeating . ' + ' . $electricityUsageTapWater  . ' + ' . $electricityUsageCooking . ') - ' . $currentElectricityUsageHeating . ' - ' . $currentElectricityUsageTapWater . ' - ' . $currentElectricityUsageCooking);
        $extraConsumptionElectricity = ($electricityUsageHeating +
                $electricityUsageTapWater +
                $electricityUsageCooking) -
            $currentElectricityUsageHeating -
            $currentElectricityUsageTapWater -
            $currentElectricityUsageCooking;
        Log::debug("C77: " . $extraConsumptionElectricity . " (meerverbruik elektra)");

        $savingsCo2 = Calculator::calculateCo2Savings($savingsGas) -
            ($extraConsumptionElectricity * Kengetallen::CO2_SAVINGS_ELECTRICITY);
        Log::debug("C78: " . $savingsCo2 . " (CO2 besparing)");

        $savingsMoney = Calculator::calculateMoneySavings($savingsGas) -
            ($extraConsumptionElectricity * Kengetallen::EURO_SAVINGS_ELECTRICITY);
        Log::debug("C79: " . $savingsMoney . " (euro besparing)");

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

        Log::debug("GRAND TOTAL: " . json_encode($result));

        return $result;
    }

    public function calculateAdvisedSystemRequiredPower()
    {
        $kfInsulationFactor = KeyFigureInsulationFactor::forInsulationFactor($this->insulationScore())->first();
        $wattPerSquareMeter = $kfInsulationFactor->energy_consumption_per_m2 ?? 140;
        $surface = $this->getAnswer('surface');

        return ($wattPerSquareMeter * $surface) / 1000;
    }

    public function calculateShareHeating(): int
    {
        // First check
        // 1) if required power - desired power >= 1: 100;
        // 2) if required power - desired power >= 0 AND required power - desired power < 1:
        // 100 for low heating temperature, 95 for 50 degrees, 85 for high heating temperature;
        // This equals using beta factor 1.

        // scenario 1
        if ($this->desiredPower - $this->requiredPower >= 1) {
            return 100;
        }

        // Use database table
        if ($this->heatingTemperature instanceof ToolQuestionCustomValue) {
            // scenario 2 (use of beta factor 1) is included in the beta factor method, so we can keep the code smaller
            $coverage = KeyFigureHeatPumpCoverage::forBetaFactor($this->betaFactor())
                ->forHeatingTemperature($this->heatingTemperature)
                ->first();

            if ($coverage instanceof KeyFigureHeatPumpCoverage){
                return $coverage->percentage;
            }
        }

        return 0;
    }

    public function lookupHeatPumpCharacteristics(): ?HeatPumpCharacteristic
    {
        $heatPumpConfigurable = ToolHelper::getServiceValueByCustomValue('heat-pump', 'new-heat-pump-type',
            $this->getAnswer('new-heat-pump-type'));

        if ($heatPumpConfigurable instanceof Model && $this->heatingTemperature instanceof ToolQuestionCustomValue) {
            return HeatPumpCharacteristic::forHeatPumpConfigurable($heatPumpConfigurable)
                ->forHeatingTemperature($this->heatingTemperature)
                ->first();
        }

        return null;
    }

    // = C61
    public function betaFactor() : float
    {
        if ($this->desiredPower - $this->requiredPower >= 0 && $this->desiredPower - $this->requiredPower < 1) {
            return 1;
        }

        return min(round($this->desiredPower / max($this->requiredPower, 1), 1), 1.0);
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
        $score = 0;

        $windowQuestions = [
            'current-living-rooms-windows' => 1.5,
            'current-sleeping-rooms-windows' => 0.5,
        ];

        $windowScore = $this->insulationSubScore($windowQuestions);
        $score += ($windowScore / count($windowQuestions));

        $toolQuestions = [
            'current-wall-insulation' => 1,
            'current-floor-insulation' => 1,
            'current-roof-insulation' => 1,
        ];

        $score += $this->insulationSubScore($toolQuestions);

        // count +1 because window also counts as one factor.
        return round($score / (count($toolQuestions) + 1), 1);
    }

    protected function insulationSubScore($questions) : float {
        $score = 0;

        foreach ($questions as $toolQuestion => $weight) {
            /** @var ElementValue $elementValue */
            $elementValue = ElementValue::find($this->getAnswer($toolQuestion));

            $factor = optional($elementValue)->insulation_factor ?? 1;
            if ($factor <= 1) {
                // If the state of this element is bad we want to advise the user to fix this first
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