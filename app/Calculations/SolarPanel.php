<?php

namespace App\Calculations;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Kengetallen;
use App\Helpers\KeyFigures\PvPanels\KeyFigures;
use App\Helpers\Translation;
use App\Models\Building;
use App\Models\InputSource;
use App\Models\PvPanelLocationFactor;
use App\Models\PvPanelOrientation;
use App\Models\PvPanelYield;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use App\Models\User;
use Carbon\Carbon;

class SolarPanel
{
    /**
     * Calculate the savings for the solar panel.
     *
     * @param User $user
     *
     * @return array
     */
    public static function calculate(Building $building, array $calculateData)
    {
        $result = [
            'yield_electricity' => 0,
            'raise_own_consumption' => 0,
            'savings_co2' => 0,
            'savings_money' => 0,
            'cost_indication' => 0,
            'interest_comparable' => 0,
            'year' => null,
        ];

        $buildingPvPanels = $calculateData['building_pv_panels'] ?? [];
        // // \Log::debug('Data input: '.json_encode($buildingPvPanels));
        $amountElectricity = $calculateData['user_energy_habits']['amount_electricity'] ?? 0;
        $peakPower = $buildingPvPanels['peak_power'] ?? 0;
        // check if its set and if its a number since input like 12-14 is allowed.
        $panels = isset($buildingPvPanels['number']) && is_numeric($buildingPvPanels['number']) ? $buildingPvPanels['number'] : 0;
        $orientationId = $buildingPvPanels['pv_panel_orientation_id'] ?? 0;
        $angle = $buildingPvPanels['angle'] ?? 0;

        $orientation = PvPanelOrientation::find($orientationId);

        $locationFactor = KeyFigures::getLocationFactor($building->postal_code);
        $helpFactor = 0;
        if ($orientation instanceof PvPanelOrientation && $angle > 0) {
            $yield = KeyFigures::getYield($orientation, $angle);
            if ($yield instanceof PvPanelYield && $locationFactor instanceof PvPanelLocationFactor) {
                $helpFactor = $yield->yield * $locationFactor->factor;
            }
        }

        if ($peakPower > 0) {

            $number = 0;
            // we cant calculate the amount of panels if someone has a negative amount of electricity
            if ($amountElectricity > 0) {
                $number = ceil(($amountElectricity / $helpFactor) / $peakPower);
            }
            // \Log::debug(__METHOD__.' Advised number of panels: '.$number.' = ceil(( '.$amountElectricity.' / '.$helpFactor.') / '.$peakPower.')');
            $result['advice'] = Translation::translate('solar-panels.advice-text', ['number' => $number]);
            $wp = $panels * $peakPower;
            $result['total_power'] = Translation::translate('solar-panels.total-power', ['wp' => $wp]);

            $result['yield_electricity'] = $wp * $helpFactor;
            // \Log::debug(__METHOD__.' Electricity yield: '.$result['yield_electricity'].' = '.$wp.' * '.$helpFactor);

            $result['raise_own_consumption'] = $amountElectricity <= 0 ? 0 : ($result['yield_electricity'] / $amountElectricity) * 100;
            // \Log::debug(__METHOD__.' % of own consumption: '.$result['raise_own_consumption'].' = ('.$result['yield_electricity'].' / '.$amountElectricity.') * 100');

            $result['savings_co2'] = $result['yield_electricity'] * Kengetallen::CO2_SAVINGS_ELECTRICITY;
            $result['savings_money'] = $result['yield_electricity'] * KeyFigures::COST_KWH;
            $result['cost_indication'] = $wp * KeyFigures::COST_WP;
            $result['interest_comparable'] = number_format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);


            $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
            $hasPanelsQuestion = ToolQuestion::findByShort('has-solar-panels');
            if ($hasPanelsQuestion instanceof ToolQuestion) {
                $answer = $building->getAnswer($masterInputSource, $hasPanelsQuestion);
                $toolQuestionCustomValue = $hasPanelsQuestion->toolQuestionCustomValues()
                    ->where('short', $answer)
                    ->first();

                if ($toolQuestionCustomValue instanceof ToolQuestionCustomValue) {
                    $currentYear = Carbon::now()->year;

                    if ($toolQuestionCustomValue->short == 'no') {
                        // No panels
                        $result['year'] = $currentYear;
                    } else {
                        // The user has solar panels, let's see if there's an age for it
                        $ageQuestion = ToolQuestion::findByShort('solar-panels-placed-date');
                        if ($ageQuestion instanceof ToolQuestion) {
                            $answer = $building->getAnswer($masterInputSource, $ageQuestion);
                            // if its numeric its probably a year.
                            if (is_numeric($answer)) {
                                $diff = now()->format('Y') - $answer;

                                // If it's not 25 years old
                                $result['year'] = $diff < 25 ? $currentYear + 5 : $currentYear;
                            } else {
                                // No placing date available. We will assume it's fine
                                $result['year'] = $currentYear;
                            }
                        }
                    }
                }
            }
        }

        return $result;
    }
}
