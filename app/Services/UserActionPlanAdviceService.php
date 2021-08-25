<?php

namespace App\Services;

use App\Helpers\Calculator;
use App\Helpers\Number;
use App\Helpers\NumberFormatter;
use App\Helpers\StepHelper;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\RoofType;
use App\Models\Step;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
use Carbon\Carbon;

class UserActionPlanAdviceService
{
    const CATEGORY_COMPLETE = 'complete';
    const CATEGORY_TO_DO = 'to-do';
    const CATEGORY_LATER = 'later';

    public static function getCategories(): array
    {
        return [
            self::CATEGORY_COMPLETE => self::CATEGORY_COMPLETE,
            self::CATEGORY_TO_DO => self::CATEGORY_TO_DO,
            self::CATEGORY_LATER => self::CATEGORY_LATER,
        ];
    }

    /**
     * Method to delete the user action plan advices for a given user, input source and step.
     *
     * @throws \Exception
     */
    public static function clearForStep(User $user, InputSource $inputSource, Step $step)
    {
        UserActionPlanAdvice::forMe($user)
            ->forInputSource($inputSource)
            ->forStep($step)
            ->delete();
    }

    /**
     * Method to retrieve the advice year based on the step or when available measure interest level.
     *
     * @return int|null
     */
    public static function getAdviceYear(UserActionPlanAdvice $userActionPlanAdvice)
    {
        $adviceYear = null;
        $step = $userActionPlanAdvice->step;
        $buildingOwner = $userActionPlanAdvice->user;
        $measureApplication = $userActionPlanAdvice->userActionPlanAdvisable;

        // set the default user interest on the step.
        $userInterest = $buildingOwner->userInterestsForSpecificType(get_class($step), $step->id)->with('interest')->first();

        // try to obtain a specific interest on the measure application
        $userInterestOnMeasureApplication = $buildingOwner
            ->userInterestsForSpecificType(get_class($measureApplication), $measureApplication->id)
            ->with('interest')
            ->first();

        // when thats available use that.
        if ($userInterestOnMeasureApplication instanceof UserInterest) {
            $userInterest = $userInterestOnMeasureApplication;
        }

        if (! $userInterest instanceof UserInterest) {
            return $adviceYear;
        }
        if (1 == $userInterest->interest->calculate_value) {
            $adviceYear = Carbon::now()->year;
        }
        if (2 == $userInterest->interest->calculate_value) {
            $adviceYear = Carbon::now()->year + 5;
        }

        return $adviceYear;
    }

    /**
     * Method to return a year or string with no year.
     *
     * @note this is NOT the same as getAdviceYear.
     * This will returned the planned_year as first option.
     *
     * @return array|int|string|null
     */
    public static function getYear(UserActionPlanAdvice $userActionPlanAdvice)
    {
        // always try to get the planned year, as this is what de user gave as input
        $year = $userActionPlanAdvice->planned_year ?? $userActionPlanAdvice->year;

        // when the year is empty try to get one last resort.
        if (is_null($year)) {
            $year = UserActionPlanAdviceService::getAdviceYear($userActionPlanAdvice) ?? __('woningdossier.cooperation.tool.my-plan.no-year');
        }

        return $year;
    }

    /**
     * Method to return input sources that have an action plan advice, on a building.
     *
     * @return UserActionPlanAdvice[]|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public static function availableInputSourcesForActionPlan(User $user)
    {
        return UserActionPlanAdvice::withoutGlobalScope(GetValueScope::class)
            ->where('user_id', $user->id)
            ->select('input_source_id')
            ->groupBy('input_source_id')
            ->get()
            ->map(function ($userActionPlanAdvice) {
                return $userActionPlanAdvice->inputSource;
            });
    }

    /**
     * Get the personal plan for a user and its input source.
     */
    public static function getPersonalPlan(User $user, InputSource $inputSource): array
    {
        $advices = self::getCategorizedActionPlan($user, $inputSource);

        $sortedAdvices = [];

        foreach ($advices as $measureType => $stepAdvices) {
            foreach ($stepAdvices as $stepSlug => $advicesForStep) {
                foreach ($advicesForStep as $advice) {
                    if ($advice->planned) {
                        $savingsMoney = $advice->savings_money;
                        $year = self::getYear($advice);

                        // if its a string, the $year contains 'geen jaartal'
                        if (is_string($year)) {
                            $costYear = Carbon::now()->year;
                        } else {
                            $costYear = $year;
                        }
                        if (! array_key_exists($year, $sortedAdvices)) {
                            $sortedAdvices[$year] = [];
                        }

                        if ('ntb.' !== $savingsMoney) {
                            $savingsMoney = is_null($advice->savings_money) ? 0 : NumberFormatter::round(Calculator::indexCosts($advice->savings_money, $costYear));
                            $savingsMoney = Number::isNegative($savingsMoney) ? 0 : $savingsMoney;
                        }

                        // get step from advice
                        $step = $advice->step;

                        if (! array_key_exists($step->name, $sortedAdvices[$year])) {
                            $sortedAdvices[$year][$step->name] = [];
                        }

                        $savingsGas = is_null($advice->savings_gas) ? 0 : NumberFormatter::round($advice->savings_gas);
                        $savingsElectricity = is_null($advice->savings_electricity) ? 0 : NumberFormatter::round($advice->savings_electricity);

                        $sortedAdvices[$year][$step->name][$advice->userActionPlanAdvisable->short] = [
                            'interested' => $advice->planned,
                            'advice_id' => $advice->id,
                            'warning' => $advice->warning,
                            'measure' => $advice->userActionPlanAdvisable->measure_name,
                            'measure_short' => $advice->userActionPlanAdvisable->short,                    // In the table the costs are indexed based on the advice year
                            // Now re-index costs based on user planned year in the personal plan
                            'costs' => NumberFormatter::round(Calculator::indexCosts($advice->costs['from'] ?? 0, $costYear)),
                            'savings_gas' => Number::isNegative($savingsGas) ? 0 : $savingsGas,
                            'savings_electricity' => Number::isNegative($savingsElectricity) ? 0 : $savingsElectricity,
                            'savings_money' => $savingsMoney,
                        ];
                    }
                }
            }
        }

        ksort($sortedAdvices);

        return $sortedAdvices;
    }

    /**
     * Check if can return the savings money or have to return "ntb."
     * We do this when the selected insulation is "Matige isolatie (tot 8 cm isolatie)" or higher also known als calculate_value >= 3.
     *
     * @param $savingsMoney
     *
     * @return string|int
     */
    public static function checkSavingsMoney(UserActionPlanAdvice $advice, $savingsMoney)
    {
        $user = $advice->user;
        $step = $advice->step;

        if ('roof-insulation' == $step->short) {
            // the energy saving measure application shorts.
            $flatRoofMeasureApplications = ['roof-insulation-flat-replace-current', 'roof-insulation-flat-current'];
            $pitchedRoofMeasureApplications = ['roof-insulation-pitched-replace-tiles', 'roof-insulation-pitched-inside'];

            // check the current advice its measure application, this way we can determine which roofType we have to check
            if (in_array($advice->userActionPlanAdvisable->short, $pitchedRoofMeasureApplications)) {
                $roofType = RoofType::findByShort('pitched');
            }
            if (in_array($advice->userActionPlanAdvisable->short, $flatRoofMeasureApplications)) {
                $roofType = RoofType::findByShort('flat');
            }

            // get the right matching roof type.
            $buildingRoofType = $user->building->roofTypes()->forInputSource($advice->inputSource)->where('roof_type_id', $roofType->id)->first();

            if ($buildingRoofType->elementValue->calculate_value >= 3) {
                $savingsMoney = 'ntb.';
            }
        } elseif (in_array($step->short, ['floor-insulation', 'wall-insulation'])) {
            $elementShort = array_search($step->short, StepHelper::ELEMENT_TO_SHORT);

            if ($user->building->getBuildingElement($elementShort, $advice->inputSource)->elementValue->calculate_value >= 3) {
                $savingsMoney = 'ntb.';
            }
        }

        return $savingsMoney;
    }

    /**
     * Get the action plan categorized under measure type.
     *
     * @param bool $withAdvices
     *
     * @return array
     */
    public static function getCategorizedActionPlan(User $user, InputSource $inputSource, $withAdvices = true)
    {
        $result = [];

        $advices = UserActionPlanAdvice::forInputSource($inputSource)
            ->where('user_id', $user->id)
            ->with('user.building', 'userActionPlanAdvisable', 'step')
            ->where('user_action_plan_advisable_type', MeasureApplication::class)
            ->orderBy('step_id', 'asc')
            ->orderBy('year', 'asc')
            ->get();

        /** @var UserActionPlanAdvice $advice */
        foreach ($advices as $advice) {
            if ($advice->step instanceof Step) {
                /** @var MeasureApplication $measureApplication */
                $measureApplication = $advice->userActionPlanAdvisable;

                if (is_null($advice->year)) {
                    $advice->year = self::getAdviceYear($advice);
                }

                // check if we have to set the $savingsMoney to ntb.
                if ('energy_saving' == $advice->userActionPlanAdvisable->measure_type) {
                    $advice->savings_money = self::checkSavingsMoney($advice, $advice->savings_money);
                }

                // if advices are not desirable and the measureApplication is not an advice it will be added to the result
                if (! $withAdvices && ! $measureApplication->isAdvice()) {
                    $result[$measureApplication->measure_type][$advice->step->slug][$measureApplication->short] = $advice;
                }

                // if advices are desirable we always add it.
                if ($withAdvices) {
                    $result[$measureApplication->measure_type][$advice->step->slug][$measureApplication->short] = $advice;
                }
            }
        }

        ksort($result);

        $result = self::checkCoupledMeasuresAndMaintenance($result);

        return $result;
    }

    /**
     * Get the right warning text.
     *
     * @param $translationKey
     *
     * @return array|string|null
     */
    public static function getWarning($translationKey)
    {
        // for the pdf we want a different warning translations then for the my-plan page
        // when the app is running in the console we change the group to the pdf translations
        $translationGroup = 'my-plan.warnings';
        if (app()->runningInConsole()) {
            $translationGroup = 'pdf/user-report.warnings';
        }

        return __("{$translationGroup}.{$translationKey}");
    }

    /**
     * Method to add warning to a categorized action plan.
     *
     * @return array
     */
    public static function checkCoupledMeasuresAndMaintenance(array $categorizedActionPlan)
    {
        $energySaving = $categorizedActionPlan['energy_saving'] ?? [];
        $maintenance = $categorizedActionPlan['maintenance'] ?? [];

        if (isset($maintenance['wall-insulation']) && isset($energySaving['wall-insulation'])) {
            $maintenanceForWallInsulation = $maintenance['wall-insulation'];
            $energySavingForWallInsulation = $energySaving['wall-insulation'];

            // it isn't possible to add spouwmuurisolatie on a painted / plastered wall.
            if (isset($maintenanceForWallInsulation['paint-wall']) && isset($energySavingForWallInsulation['cavity-wall-insulation'])) {
                $categorizedActionPlan['energy_saving']['wall-insulation']['cavity-wall-insulation']['warning'] = static::getWarning('wall-insulation.cavity-wall-with-paint');
                $categorizedActionPlan['maintenance']['wall-insulation']['paint-wall']['warning'] = static::getWarning('wall-insulation.cavity-wall-with-paint');
            }
        }

        // we will have to compare the year / interest levels of the energy saving and maintenance with each other
        if (isset($maintenance['roof-insulation']) && isset($energySaving['roof-insulation'])) {
            $maintenanceForRoofInsulation = $maintenance['roof-insulation'];
            $energySavingForRoofInsulation = $energySaving['roof-insulation'];

            // flat roof
            if (isset($energySavingForRoofInsulation['roof-insulation-flat-replace-current']) && $energySavingForRoofInsulation['roof-insulation-flat-replace-current']['planned']) {
                $energySavingRoofInsulationFlatReplaceCurrentYear = $energySavingForRoofInsulation['roof-insulation-flat-replace-current']['planned_year'];
                $maintenanceReplaceRoofInsulationYear = $maintenanceForRoofInsulation['replace-roof-insulation']['planned_year'];

                if (! $maintenanceForRoofInsulation['replace-roof-insulation']['planned']) {
                    // set warning
                    $categorizedActionPlan['maintenance']['roof-insulation']['replace-roof-insulation']['warning'] = static::getWarning('roof-insulation.check-order');
                    $categorizedActionPlan['energy_saving']['roof-insulation']['roof-insulation-flat-replace-current']['warning'] = static::getWarning('roof-insulation.check-order');
                // both were planned, so check whether the planned year is the same
                } elseif ($energySavingRoofInsulationFlatReplaceCurrentYear !== $maintenanceReplaceRoofInsulationYear) {
                    // set warning
                    $categorizedActionPlan['maintenance']['roof-insulation']['replace-roof-insulation']['warning'] = static::getWarning('roof-insulation.planned-year');
                    $categorizedActionPlan['energy_saving']['roof-insulation']['roof-insulation-flat-replace-current']['warning'] = static::getWarning('roof-insulation.planned-year');
                }
            }

            // pitched roof
            if (isset($energySavingForRoofInsulation['roof-insulation-pitched-replace-tiles']) && $energySavingForRoofInsulation['roof-insulation-pitched-replace-tiles']['planned']) {
                $energySavingRoofInsulationPitchedReplaceTilesYear = $energySavingForRoofInsulation['roof-insulation-pitched-replace-tiles']['planned_year'];
                $maintenanceReplaceTilesYear = $maintenanceForRoofInsulation['replace-tiles']['planned_year'];
                if (! $maintenanceForRoofInsulation['replace-tiles']['planned']) {
                    // set warning
                    $categorizedActionPlan['maintenance']['roof-insulation']['replace-tiles']['warning'] = static::getWarning('roof-insulation.check-order');
                    $categorizedActionPlan['energy_saving']['roof-insulation']['roof-insulation-pitched-replace-tiles']['warning'] = static::getWarning('roof-insulation.check-order');
                // both were planned, so check whether the planned year is the same
                } elseif ($energySavingRoofInsulationPitchedReplaceTilesYear !== $maintenanceReplaceTilesYear) {
                    // set warning
                    $categorizedActionPlan['maintenance']['roof-insulation']['replace-tiles']['warning'] = static::getWarning('roof-insulation.planned-year');
                    $categorizedActionPlan['energy_saving']['roof-insulation']['roof-insulation-pitched-replace-tiles']['warning'] = static::getWarning('roof-insulation.planned-year');
                }
            }
        }

        if (isset($energySaving['ventilation'])) {
            $energySavingForVentilation = $energySaving['ventilation'];

            foreach ($energySavingForVentilation as $measureShort => $advice) {
                if (empty(($advice->costs['from'] ?? null)) && empty($advice->savings_gas) && empty($advice->savings_electricity) && empty($advice->savings_money)) {
                    // this will have to change in the near future for the pdf.
                    $categorizedActionPlan['energy_saving']['ventilation'][$measureShort]['warning'] = static::getWarning('ventilation');
                }
            }
        }

        return $categorizedActionPlan;
    }
}
