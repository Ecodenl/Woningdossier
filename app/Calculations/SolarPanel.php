<?php

namespace App\Calculations;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\HoomdossierSession;
use App\Helpers\Kengetallen;
use App\Helpers\KeyFigures\PvPanels\KeyFigures;
use App\Helpers\NumberFormatter;
use App\Helpers\Translation;
use App\Models\Building;
use App\Models\Interest;
use App\Models\PvPanelLocationFactor;
use App\Models\PvPanelOrientation;
use App\Models\PvPanelYield;
use App\Models\User;
use Carbon\Carbon;

class SolarPanel {

    /**
     * Calculate the savings for the solar panel
     *
     * @param  Building  $building
     * @param  User  $user
     * @param  array  $calculateData
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
\Log::debug("Data input: " . json_encode($buildingPvPanels));
        $amountElectricity = $calculateData['user_energy_habits']['amount_electricity'] ?? 0;
        $peakPower = $buildingPvPanels['peak_power'] ?? 0;
        $panels = $buildingPvPanels['number'] ?? 0;
        $orientationId = $buildingPvPanels['pv_panel_orientation_id'] ?? 0;
        $angle = $buildingPvPanels['angle'] ?? 0;


        $interests = $calculateData['interest'] ?? '';
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
            $number = ceil(($amountElectricity / $helpFactor) / $peakPower);
            \Log::debug(__METHOD__ . " Advised number of panels: " . $number . " = ceil(( " . $amountElectricity . " / " . $helpFactor . ") / " . $peakPower . ")");
            $result['advice'] = Translation::translate('solar-panels.advice-text', ['number' => $number]);
            $wp = $panels * $peakPower;
            $result['total_power'] = Translation::translate('solar-panels.total-power', ['wp' => $wp]);

            $result['yield_electricity'] = $wp * $helpFactor;
            \Log::debug(__METHOD__ . " Electricity yield: " . $result['yield_electricity'] . " = " . $wp . " * " . $helpFactor);

            $result['raise_own_consumption'] = $amountElectricity <= 0 ? 0 : ($result['yield_electricity'] / $amountElectricity) * 100;
            \Log::debug(__METHOD__ . " % of own consumption: " . $result['raise_own_consumption'] . " = (" . $result['yield_electricity'] . " / " . $amountElectricity . ") * 100");

            $result['savings_co2'] = $result['yield_electricity'] * Kengetallen::CO2_SAVINGS_ELECTRICITY;
            $result['savings_money'] = $result['yield_electricity'] * KeyFigures::COST_KWH;
            $result['cost_indication'] = $wp * KeyFigures::COST_WP;
            $result['interest_comparable'] = number_format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);

            foreach ($interests as $type => $interestTypes) {
                foreach ($interestTypes as $typeId => $interestId) {
                    $interest = Interest::find($interestId);
                }
            }

            $currentYear = Carbon::now()->year;
            if (1 == $interest->calculate_value) {
                $result['year'] = $currentYear;
            } elseif (2 == $interest->calculate_value) {
                $result['year'] = $currentYear + 5;
            }
        }

        if ($helpFactor >= 0.84) {
            $result['performance'] = [
                'alert' => 'success',
                'text' => Translation::translate('solar-panels.indication-for-costs.performance.ideal'),
            ];
        } elseif ($helpFactor < 0.70) {
            $result['performance'] = [
                'alert' => 'danger',
                'text' => Translation::translate('solar-panels.indication-for-costs.performance.no-go'),
            ];
        } else {
            $result['performance'] = [
                'alert' => 'warning',
                'text' => Translation::translate('solar-panels.indication-for-costs.performance.possible'),
            ];
        }

        return $result;
    }
}