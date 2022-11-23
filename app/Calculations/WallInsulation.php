<?php

namespace App\Calculations;

use App\Helpers\Calculation\BankInterestCalculator;
use App\Helpers\Calculator;
use App\Helpers\KeyFigures\WallInsulation\Temperature;
use App\Models\Building;
use App\Models\ElementValue;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\UserEnergyHabit;
use Carbon\Carbon;

class WallInsulation
{
    /**
     * Calculate the wall insulation costs and savings etc.
     *
     * @param UserEnergyHabit|null $energyHabit
     *
     * @return array;
     */
    public static function calculate(Building $building, InputSource $inputSource, $energyHabit, array $calculateData): array
    {
        $buildingFeatureData = $calculateData['building_features'];
        $cavityWall = $buildingFeatureData['cavity_wall'] ?? -1;
        $elements = $calculateData['element'] ?? [];
        $facadeSurface = $buildingFeatureData['insulation_wall_surface'] ?? 0;

        // Note: these answer options are hardcoded in template
        $isPlastered = 2 != (int) ($buildingFeatureData['facade_plastered_painted'] ?? 2);

        $result = [
            'savings_gas' => 0,
            'paint_wall' => [
                'costs' => 0,
                'year' => 0,
            ],
        ];
        $cavityWallAdvice = [
            1 => Temperature::WALL_INSULATION_JOINTS,
            2 => Temperature::WALL_INSULATION_FACADE,
            0 => Temperature::WALL_INSULATION_RESEARCH,
        ];

        $advice = $cavityWallAdvice[$cavityWall] ?? Temperature::WALL_INSULATION_JOINTS;

        $insulationAdvice = MeasureApplication::findByShort($advice);

        // alert the user that its not possible to insulate a painted / plastered wall.
        // important to do this after the insulation advice is set, otherwise it wil fail.

        // 1 = yes
        if ($isPlastered && $cavityWall == 1) {
            $advice = "alerts.description.title";
        }
        $result['insulation_advice'] = __('wall-insulation.'.$advice);

        $elementValueId = array_shift($elements);
        $elementValue = ElementValue::find($elementValueId);
        if ($elementValue instanceof ElementValue && $energyHabit instanceof UserEnergyHabit) {
            $result['savings_gas'] = Calculator::calculateGasSavings($building, $inputSource, $elementValue, $energyHabit, $facadeSurface, $advice);
        }

        $result['savings_co2'] = Calculator::calculateCo2Savings($result['savings_gas']);
        $result['savings_money'] = round(Calculator::calculateMoneySavings($result['savings_gas']));
        $result['cost_indication'] = Calculator::calculateCostIndication($facadeSurface, $insulationAdvice);
        $result['interest_comparable'] = number_format(BankInterestCalculator::getComparableInterest($result['cost_indication'], $result['savings_money']), 1);

        $measureApplication = MeasureApplication::where('short', '=', 'repair-joint')->first();
        //$measureApplication = MeasureApplication::where('measure_name->nl', 'Reparatie voegwerk')->first(['measure_applications.*']);
        $surfaceId = $buildingFeatureData['wall_joints'] ?? 1;
        $wallJointsSurface = FacadeSurface::find($surfaceId);
        $number = 0;
        $year = null;
        if ($wallJointsSurface instanceof FacadeSurface) {
            $number = $wallJointsSurface->calculate_value;
            $year = Carbon::now()->year + $wallJointsSurface->term_years;
        }
        $costs = Calculator::calculateMeasureApplicationCosts($measureApplication, $number, $year, false);
        $result['repair_joint'] = compact('costs', 'year');

        $measureApplication = MeasureApplication::where('short', '=', 'clean-brickwork')->first();
        //$measureApplication = MeasureApplication::where('measure_name->nl', 'Reinigen metselwerk')->first(['measure_applications.*']);
        $surfaceId = $buildingFeatureData['contaminated_wall_joints'] ?? 1;
        $wallJointsSurface = FacadeSurface::find($surfaceId);
        $number = 0;
        $year = null;
        if ($wallJointsSurface instanceof FacadeSurface) {
            $number = $wallJointsSurface->calculate_value;
            $year = Carbon::now()->year + $wallJointsSurface->term_years;
        }
        $costs = Calculator::calculateMeasureApplicationCosts($measureApplication, $number, $year, false);
        $result['clean_brickwork'] = compact('costs', 'year');

        $measureApplication = MeasureApplication::where('short', '=', 'impregnate-wall')->first();
        //$measureApplication = MeasureApplication::where('measure_name->nl', 'Impregneren gevel')->first(['measure_applications.*']);
        $surfaceId = $buildingFeatureData['contaminated_wall_joints'] ?? 1;
        $wallJointsSurface = FacadeSurface::find($surfaceId);
        $number = 0;
        $year = null;
        if ($wallJointsSurface instanceof FacadeSurface) {
            $number = $wallJointsSurface->calculate_value;
            $year = Carbon::now()->year + $wallJointsSurface->term_years;
        }
        $costs = Calculator::calculateMeasureApplicationCosts($measureApplication, $number, $year, false);

        $result['impregnate_wall'] = compact('costs', 'year');

        if ($isPlastered) {
            $measureApplication = MeasureApplication::where('short', '=', 'paint-wall')->first();
            //$measureApplication = MeasureApplication::where('measure_name->nl', 'Gevelschilderwerk op stuk- of metselwerk')->first(['measure_applications.*']);
            $surfaceId = $buildingFeatureData['facade_plastered_surface_id'];
            $facadePlasteredSurface = FacadePlasteredSurface::find($surfaceId);
            $damageId = $buildingFeatureData['facade_damaged_paintwork_id'];
            $facadeDamagedPaintwork = FacadeDamagedPaintwork::find($damageId);
            $number = 0;
            $year = null;
            if ($facadePlasteredSurface instanceof FacadePlasteredSurface && $facadeDamagedPaintwork instanceof FacadeDamagedPaintwork) {
                $number = $facadePlasteredSurface->calculate_value;
                //$year = Carbon::now()->year + $facadePlasteredSurface->term_years;
                $year = Carbon::now()->year + $facadeDamagedPaintwork->term_years;
            }
            $costs = Calculator::calculateMeasureApplicationCosts($measureApplication,
                $number,
                $year, false);
            $result['paint_wall'] = compact('costs', 'year');
        }

        return $result;
    }
}
