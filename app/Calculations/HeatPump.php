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

    protected float $requiredPower = 0;

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
        // Lookup the characteristics of the chosen heat pump (tool question answer).
        $characteristics = $this->lookupHeatPumpCharacteristics();
        // Get desired power, and set if not filled in.
        $this->desiredPower = $this->getAnswer('heat-pump-preferred-power');
        // If it wasn't answered (by person in expert or example building)
        if ($this->desiredPower <= 0 && $characteristics instanceof HeatPumpCharacteristic) {
            if ($characteristics->type === HeatPumpCharacteristic::TYPE_FULL){
                // for full: required power
                $this->desiredPower = round($this->requiredPower);
            }
            else {
                // for hybrid: fixed value / standard from table
                $this->desiredPower = round($characteristics->standard_power_kw);
            }
        }

        // This will return the share of heating, in percentages (e.g. 85%)
        $shareHeating = $this->calculateShareHeating();

        // return value affects other calculations.

        $scopTw = 0;
        if ($characteristics instanceof HeatPumpCharacteristic){
            $scopTw = $characteristics->type == HeatPumpCharacteristic::TYPE_HYBRID ? 0 : ($characteristics->scop_tap_water ?? 0); // C65
        }

        $advisedSystem = [
            'required_power' => $this->format($this->requiredPower, 1), // C60
            'desired_power' => $this->desiredPower, // C61
            'share_heating' => $shareHeating, // C62
            'share_tap_water' => $characteristics->share_percentage_tap_water ?? 0, // C63
            'scop_heating' => $characteristics->scop ?? 0, // C64
            // if hybrid: show scop_tap_water as 0 (asked by coaches)
            'scop_tap_water' => $scopTw,
        ];
        Log::debug(__METHOD__ . ' ' . json_encode($advisedSystem) . " (geadviseerd systeem)");

        // D2 = bruto
        $amountGas = $this->getAnswer('amount-gas') ?? 0;
        Log::debug("D2: " . $amountGas . " (huidig gasverbruik)");

        // new
        Log::debug("=== Heating calculate ===");
        $energyUsage = Heating::calculate($this->building, $this->inputSource, $this->answers);
        Log::debug("=== Heating calculate done ===");

        // D8
        $currentNettoGasUsageHeating = data_get($energyUsage, 'heating.current.gas.netto', 0);
        Log::debug("D8: " . $currentNettoGasUsageHeating . " (huidig netto gasverbruik verwarming)");
        // D9
        $currentNettoGasUsageTapWater = data_get($energyUsage, 'tap_water.current.gas.netto', 0);
        Log::debug("D9: " . $currentNettoGasUsageTapWater . " (huidig netto gasverbruik wtw)");
        $currentNettoGasUsageCooking = data_get($energyUsage, 'cooking.current.gas', 0);
        Log::debug("D14: " . $currentNettoGasUsageCooking . " (huidig gasverbruik koken)");

        // Now we can calculate the new energy usage
        // C68 = D8 - (D8 * C62)
        $newNettoGasUsageHeating = $currentNettoGasUsageHeating - ($currentNettoGasUsageHeating * ($advisedSystem['share_heating'] / 100));
        Log::debug("C68: " . $newNettoGasUsageHeating . " (nieuw gasverbruik verwarming)");
        // C69 = D9 - (D9 * C63)
        $newNettoGasUsageTapWater = $currentNettoGasUsageTapWater - ($currentNettoGasUsageTapWater * ($advisedSystem['share_tap_water'] / 100));
        Log::debug("C69: " . $newNettoGasUsageTapWater . " (nieuw gasverbruik wtw)");
        $newNettoGasUsageCooking = data_get($energyUsage, 'cooking.new.gas', 0);
        Log::debug("C70: " . $newNettoGasUsageCooking . " (nieuw gasverbruik koken)");

        // E71
        // if volledige warmtepomp: C68 * KeyFigures::M3_GAS_TO_KWH
        // else: 0

        // use netto
        $electricalReheating = 0;
        if (optional($characteristics)->type === HeatPumpCharacteristic::TYPE_FULL) {
            $electricalReheating = $newNettoGasUsageHeating * KeyFigures::M3_GAS_TO_KWH;
            $newNettoGasUsageHeating = 0;
        }
        Log::debug("E71: " . $electricalReheating . " (elektrisch bijverwarmen)");

        // C71 = (((D8-C68) * KeyFigures::M3_GAS_TO_KWH) / scop_heating) + E71
//        $electricityUsageHeating = ((($currentNettoGasUsageHeating - $newNettoGasUsageHeating) * KeyFigures::M3_GAS_TO_KWH) / max(
//                    $advisedSystem['scop_heating'],
//                    1
//                )) + $electricalReheating;
        $newNettoElectricityUsageHeating = data_get($energyUsage, 'heating.new.electricity.netto', 0) + $electricalReheating;
        Log::debug("C71: " . $newNettoElectricityUsageHeating . " (elektraverbruik verwarmen)");

        // C72 = ((D9-C69) * KeyFigures::M3_GAS_TO_KWH) / scop_tap_water)
        //$newNettoElectricityUsageTapWater = (($currentNettoGasUsageTapWater - $newNettoGasUsageTapWater) * KeyFigures::M3_GAS_TO_KWH) / max(
        //        $advisedSystem['scop_tap_water'],
        //        1
        //    );
        $newNettoElectricityUsageTapWater = data_get($energyUsage, 'tap_water.new.electricity.netto', 0);
        Log::debug("C72: " . $newNettoElectricityUsageTapWater . " (elektraverbruik wtw)");

        // C73 = from mapping Maatregelopties en kengetallen: B58:D60 icm future situation
        $newNettoElectricityUsageCooking = data_get($energyUsage, 'cooking.new.electricity');
        Log::debug("C73: " . $newNettoElectricityUsageCooking . " (elektraverbruik koken)");

        // D11
        $currentBrutoElectricityUsage = $this->getAnswer('amount-electricity') ?? 0;
        Log::debug("D11: " . $currentBrutoElectricityUsage . " (huidig elektraverbruik)");
        // D12
        $currentNettoElectricityUsageHeating = data_get($energyUsage, 'heating.current.electricity.netto', 0);
        Log::debug("D12: " . $currentNettoElectricityUsageHeating . " (huidig elektraverbruik verwarmen)");
        // D13
        //$currentNettoElectricityUsageTapWater = 0;
        // new
        $currentNettoElectricityUsageTapWater = data_get($energyUsage, 'tap_water.current.electricity.netto', 0);
        Log::debug("D13: " . $currentNettoElectricityUsageTapWater . " (huidig elektraverbruik wtw)");
        // D14 = from mapping Maatregelopties en kengetallen B58:D60 icm current situation
        //$currentElectricityUsageCooking = $this->energyUsageForCooking();
        // new
        $currentNettoElectricityUsageCooking = data_get($energyUsage, 'cooking.current.electricity.netto', 0);
        Log::debug("D14: " . $currentNettoElectricityUsageCooking . " (huidig elektraverbruik koken)");

        // these values aren't part of the outcome.
        // if volledige warmtepomp: D2
        // else: D2 - (C68+C69+C70)
//        $savingsGas = $amountGas;
//        if (optional($characteristics)->type !== HeatPumpCharacteristic::TYPE_FULL) {
//            Log::debug("C76: not full heatpump: savingsGas = " . $amountGas . ' - (' . $gasUsageHeating . ' + ' . $newNettoGasUsageTapWater . ' + ' . $newNettoGasUsageCooking . ')');
//            $savingsGas = $amountGas - ($gasUsageHeating + $newNettoGasUsageTapWater + $newNettoGasUsageCooking);
//        }
        // savings gas = amount gas -
        //                 (current gas usage for heating - new gas usage for heating)
        //                 (current gas usage for wtw - new gas usage for wtw)
        //                 (current gas usage for cooking - new gas usage for cooking)
        //

        //Log::debug('C76 (gasbesparing): savingsGas = amountGas - (newNettoGasUsageHeating + netNettoGasUsageTapWater + newNettoGasUsageCooking)');
        //$savingsGas = $amountGas - data_get($energyUsage, 'heating.new.gas.bruto', 0) - data_get($energyUsage, 'tap_water.new.gas.bruto', 0) - data_get($energyUsage, 'cooking.gas.electricity', 0);
        //Log::debug('C76 (gasbesparing): = ' . "$amountGas - ($newNettoGasUsageHeating + $newNettoGasUsageTapWater + $newNettoGasUsageCooking) = $savingsGas");

        $savingsGas = (data_get($energyUsage, 'heating.new.gas.bruto', 0) - data_get($energyUsage, 'heating.current.gas.bruto', 0)) +
                      (data_get($energyUsage, 'tap_water.new.gas.bruto', 0) - data_get($energyUsage, 'tap_water.current.gas.bruto', 0)) +
                      (data_get($energyUsage, 'cooking.new.gas.bruto', 0) - data_get($energyUsage, 'cooking.current.gas.bruto', 0));
        Log::debug('C76 (gasbesparing): = ' . sprintf('(%s - %s) + (%s - %s) + (%s - %s) = %s',
                data_get($energyUsage, 'heating.new.gas.bruto', 0),
                data_get($energyUsage, 'heating.current.gas.bruto', 0),
                data_get($energyUsage, 'tap_water.new.gas.bruto', 0),
                data_get($energyUsage, 'tap_water.current.gas.bruto', 0),
                data_get($energyUsage, 'cooking.new.gas.bruto', 0),
                data_get($energyUsage, 'cooking.current.gas.bruto', 0),
                $savingsGas));

        // (C71+C72+C73) - (D12-D13-D14)

//        $extraConsumptionElectricity = (data_get($energyUsage, 'heating.new.electricity.bruto', 0) - data_get($energyUsage, 'heating.current.electricity.bruto', 0)) +
//                                       (data_get($energyUsage, 'tap_water.new.electricity.bruto', 0) - data_get($energyUsage, 'tap_water.current.electricity.bruto', 0)) +
//                                       (data_get($energyUsage, 'cooking.new.electricity', 0) - data_get($energyUsage, 'cooking.current.electricity', 0));

        Log::debug("C77 (meerverbruik elektra): (newNettoElectricityUsageHeating + newNettoElectricityUsageTapWater + newNettoElectricityUsageCooking) - (currentNettoElectricityUsageHeating - currentNettoElectricityUsageTapWater - currentNettoElectricityUsageCooking)");
        $extraConsumptionElectricity = ($newNettoElectricityUsageHeating +
                $newNettoElectricityUsageTapWater +
                $newNettoElectricityUsageCooking) -
            $currentNettoElectricityUsageHeating -
            $currentNettoElectricityUsageTapWater -
            $currentNettoElectricityUsageCooking;
        Log::debug("C77 (meerverbruik elektra): (" . $newNettoElectricityUsageHeating . ' + ' . $newNettoElectricityUsageTapWater  . ' + ' . $newNettoElectricityUsageCooking . ') - ' . $currentNettoElectricityUsageHeating . ' - ' . $currentNettoElectricityUsageTapWater . ' - ' . $currentNettoElectricityUsageCooking . ' = ' . $extraConsumptionElectricity);

        $savingsCo2 = Calculator::calculateCo2Savings($savingsGas) -
            ($extraConsumptionElectricity * Kengetallen::CO2_SAVINGS_ELECTRICITY);
        Log::debug("C78: " . $savingsCo2 . " (CO2 besparing)");

        $savingsMoney = Calculator::calculateMoneySavings($savingsGas) -
            ($extraConsumptionElectricity * Kengetallen::EURO_SAVINGS_ELECTRICITY);
        Log::debug("C79: " . $savingsMoney . " (euro besparing)");

        $result = [
            'amount_gas' => $amountGas,
            'amount_electricity' => $currentBrutoElectricityUsage,
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

    public function calculateAdvisedSystemRequiredPower(): float
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
        if ($this->desiredPower - round($this->requiredPower) >= 1) {
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
            Log::debug("New heat pump: " . $heatPumpConfigurable->value);
            return HeatPumpCharacteristic::forHeatPumpConfigurable($heatPumpConfigurable)
                ->forHeatingTemperature($this->heatingTemperature)
                ->first();
        }

        return null;
    }

    // = C61
    public function betaFactor() : float
    {
        // use round for this check
        if ($this->desiredPower - round($this->requiredPower) >= 0 &&
            $this->desiredPower - round($this->requiredPower < 1)
        ) {
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