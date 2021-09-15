<?php

namespace App\Services;

use App\Helpers\Arr;
use App\Helpers\Calculator;
use App\Helpers\Number;
use App\Helpers\NumberFormatter;
use App\Helpers\StepHelper;
use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingService;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\MeasureApplication;
use App\Models\RoofType;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
use App\Scopes\GetValueScope;
use App\Scopes\VisibleScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

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
     * @param  \App\Models\User  $user
     * @param  \App\Models\InputSource  $inputSource
     * @param  \App\Models\Step  $step
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function clearForStep(User $user, InputSource $inputSource, Step $step): Collection
    {
        // TODO: ensure correct input source is passed, or remove variable and ALWAYS use master
        $inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        // Get old advices
        $oldAdvices = UserActionPlanAdvice::withoutGlobalScope(VisibleScope::class)
            ->forMe($user)
            ->forInputSource($inputSource)
            ->forStep($step)
            ->get();

        // Delete old advices
        UserActionPlanAdvice::withoutGlobalScope(VisibleScope::class)
            ->forMe($user)
            ->forInputSource($inputSource)
            ->forStep($step)
            ->delete();

        return $oldAdvices;
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
        $userInterest = $buildingOwner->userInterestsForSpecificType(get_class($step),
            $step->id)->with('interest')->first();

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
                            $savingsMoney = is_null($advice->savings_money) ? 0 : NumberFormatter::round(Calculator::indexCosts($advice->savings_money,
                                $costYear));
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
                            'measure_short' => $advice->userActionPlanAdvisable->short,
                            // In the table the costs are indexed based on the advice year
                            // Now re-index costs based on user planned year in the personal plan
                            'costs' => NumberFormatter::round(Calculator::indexCosts($advice->costs['from'] ?? 0,
                                $costYear)),
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
     * We do this when the selected insulation is "Matige isolatie (tot 8 cm isolatie)" or higher also known als
     * calculate_value >= 3.
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
            $pitchedRoofMeasureApplications = [
                'roof-insulation-pitched-replace-tiles', 'roof-insulation-pitched-inside',
            ];

            // check the current advice its measure application, this way we can determine which roofType we have to check
            if (in_array($advice->userActionPlanAdvisable->short, $pitchedRoofMeasureApplications)) {
                $roofType = RoofType::findByShort('pitched');
            }
            if (in_array($advice->userActionPlanAdvisable->short, $flatRoofMeasureApplications)) {
                $roofType = RoofType::findByShort('flat');
            }

            // get the right matching roof type.
            $buildingRoofType = $user->building->roofTypes()->forInputSource($advice->inputSource)->where('roof_type_id',
                $roofType->id)->first();

            if ($buildingRoofType->elementValue->calculate_value >= 3) {
                $savingsMoney = 'ntb.';
            }
        } elseif (in_array($step->short, ['floor-insulation', 'wall-insulation'])) {
            $elementShort = array_search($step->short, StepHelper::ELEMENT_TO_SHORT);

            if (optional($user->building->getBuildingElement($elementShort,
                    $advice->inputSource)->elementValue)->calculate_value >= 3
            ) {
                $savingsMoney = 'ntb.';
            }
        }

        return $savingsMoney;
    }

    /**
     * Get the action plan categorized under measure type.
     *
     * @param  bool  $withAdvices
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

    /**
     * Set properties from old advices on another advice
     *
     * @param  \App\Models\UserActionPlanAdvice  $userActionPlanAdvice
     * @param  \App\Models\MeasureApplication  $measureApplication
     * @param  \Illuminate\Database\Eloquent\Collection  $oldAdvices
     */
    public static function checkOldAdvices(UserActionPlanAdvice $userActionPlanAdvice, MeasureApplication $measureApplication, Collection $oldAdvices)
    {
        $oldAdvice = $oldAdvices->where('user_action_plan_advisable_type', '=', MeasureApplication::class)
            ->where('user_action_plan_advisable_id', '=', $measureApplication->id)->first();
        // This measure was set before. We ensure they stay
        if ($oldAdvice instanceof UserActionPlanAdvice) {
            $userActionPlanAdvice->category = $oldAdvice->category;
            $userActionPlanAdvice->visible = $oldAdvice->visible;
            $userActionPlanAdvice->order = $oldAdvice->order;
        }
    }

    /**
     * Set the visibility of a user action plan advice
     *
     * @param  \App\Models\UserActionPlanAdvice  $userActionPlanAdvice
     */
    public static function setAdviceVisibility(UserActionPlanAdvice $userActionPlanAdvice)
    {
        $building = $userActionPlanAdvice->user->building;

        // Chance of a building not being set is small, but not impossible!
        if ($building instanceof Building && $building->hasAnsweredExpertQuestion()) {
            // We don't need to check further, if the building has answered an expert question, then it will be shown
            return true;
        }

        // It's always going to be visible by default. Further logic follows.
        $visible = true;

        $advisable = $userActionPlanAdvice->userActionPlanAdvisable;

        if ($advisable instanceof MeasureApplication) {
            // Interest map based on calculate_value
            $interestMap = [
                1 => true,
                2 => true,
                3 => true,
                4 => false,
                5 => true,
            ];
            // Define visible based on example building interest if available
            $interest = static::getInterestForMeasure($userActionPlanAdvice->user, $advisable);
            if ($interest instanceof Interest) {
                $visible = $interestMap[$interest->calculate_value];
            } elseif ($advisable->measure_type === MeasureApplication::MAINTENANCE) {
                // Else if it's maintenance, change logic. We never show maintenance, with 2 exceptions (of course...)
                $visible = false;

                $shorts = ['replace-tiles', 'replace-roof-insulation'];

                // Logic is simple for these 2 exceptions. If it's within 5 years, then we _do_ show it
                if (in_array($advisable->short, $shorts) && ! is_null($userActionPlanAdvice->year)) {
                    $visible = $userActionPlanAdvice->year - now()->format('Y') <= 5;
                }
            }
        }

        $userActionPlanAdvice->visible = $visible;
    }

    /**
     * Set the category of a user action plan advice
     *
     * @param  \App\Models\UserActionPlanAdvice  $userActionPlanAdvice
     */
    public static function setAdviceCategory(UserActionPlanAdvice $userActionPlanAdvice)
    {
        // The logic for a category is a bit more complex than visibility. We define to-do as default
        $category = static::CATEGORY_TO_DO;

        $advisable = $userActionPlanAdvice->userActionPlanAdvisable;

        if ($advisable instanceof MeasureApplication) {
            // Interest map based on calculate_value
            $interestMap = [
                1 => static::CATEGORY_TO_DO,
                2 => static::CATEGORY_LATER,
                3 => static::CATEGORY_LATER,
                4 => static::CATEGORY_COMPLETE, // Shouldn't be visible
                5 => static::CATEGORY_COMPLETE,
            ];

            // Define category based on example building interest if available
            $interest = static::getInterestForMeasure($userActionPlanAdvice->user, $advisable);
            if ($interest instanceof Interest) {
                $category = $interestMap[$interest->calculate_value];
            } else {
                // No interest defined. We need to check if the measure is available for the user...
                $building = $userActionPlanAdvice->user->building;

                // Chance of a building not being set is small, but not impossible!
                if ($building instanceof Building) {
                    $category = static::getCategoryFromMeasure($building, $advisable);
                }
            }
        }

        $userActionPlanAdvice->category = $category;
    }

    public static function getInterestForMeasure(User $user, MeasureApplication $measureApplication)
    {
        // Let's get the master input source. We need this for interests
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $userInterest = UserInterest::forInputSource($masterInputSource)
            ->where('user_id', $user->id)
            ->has('interest')
            ->whereHasMorph('interestedIn',
                MeasureApplication::class,
                function (Builder $query) use ($measureApplication) {
                    $query->where('id', $measureApplication->id);
                }
            )
            ->first();

        return optional($userInterest)->interest;
    }

    public static function getCategoryFromMeasure(Building $building, MeasureApplication $measureApplication): string
    {
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        // We only allow energy saving measures
        if ($measureApplication->measure_type === MeasureApplication::ENERGY_SAVING) {
            // Measures have their own category logic...
            $categorization = [
                'floor-insulation' => 'insulation',
                'bottom-insulation' => 'insulation',
                'floor-insulation-research' => 'insulation',
                'cavity-wall-insulation' => 'insulation',
                'facade-wall-insulation' => 'insulation',
                'wall-insulation-research' => 'insulation',
                'glass-in-lead' => 'glass',
                'hrpp-glass-only' => 'glass',
                'hrpp-glass-frames' => 'glass',
                'hr3p-frames' => 'glass',
                'crack-sealing' => 'crack-sealing',
                'roof-insulation-pitched-inside' => 'insulation',
                'roof-insulation-pitched-replace-tiles' => 'insulation',
                'roof-insulation-flat-current' => 'insulation',
                'roof-insulation-flat-replace-current' => 'insulation',
                'high-efficiency-boiler-replace' => 'hr-boiler',
                'heater-place-replace' => 'sun-boiler',
                'solar-panels-place-replace' => 'solar-panels',
                'ventilation-balanced-wtw' => 'ventilation',
                'ventilation-decentral-wtw' => 'ventilation',
                'ventilation-demand-driven' => 'ventilation',
            ];

            $logicShort = $categorization[$measureApplication->short];
            switch ($logicShort) {
                case 'insulation':
                    // Multiple types of insulation, we check based on measure which question is relevant
                    $floorShorts = ['floor-insulation', 'bottom-insulation', 'floor-insulation-research'];
                    $wallShorts = ['cavity-wall-insulation', 'facade-wall-insulation', 'wall-insulation-research'];
                    $roofShorts = [
                        'roof-insulation-pitched-inside', 'roof-insulation-pitched-replace-tiles',
                        'roof-insulation-flat-current', 'roof-insulation-flat-replace-current',
                    ];

                    // Define tool question based on the measure short
                    $toolQuestionShort = in_array($measureApplication->short, $floorShorts) ? 'current-floor-insulation'
                        : (in_array($measureApplication->short, $wallShorts) ? 'current-wall-insulation'
                            : 'current-roof-insulation');

                    $relevantQuestion = ToolQuestion::findByShort($toolQuestionShort);
                    if ($relevantQuestion instanceof ToolQuestion) {
                        $answer = $building->getAnswer($masterInputSource, $relevantQuestion);
                        $elementValue = ElementValue::find($answer);
                        if ($elementValue instanceof ElementValue) {
                            // If the value is 1 or 2 (onbekend, geen), we want it in to-do
                            // If it's "niet van toepassing" it should be hidden, so we don't worry about it
                            $category = $elementValue->calculate_value > 2 ? static::CATEGORY_COMPLETE
                                : static::CATEGORY_TO_DO;
                        }
                    }
                    break;

                case 'glass':
                    $relevantQuestions = ToolQuestion::findByShorts([
                        'current-living-rooms-windows', 'current-sleeping-rooms-windows',
                    ]);
                    $answers = [];
                    foreach ($relevantQuestions as $relevantQuestion) {
                        $answer = $building->getAnswer($masterInputSource, $relevantQuestion);
                        $elementValue = ElementValue::find($answer);

                        if ($elementValue instanceof ElementValue) {
                            // Glass has no calculate value... we use the order
                            $answers[$relevantQuestion->short] = $elementValue->order;
                        }
                    }

                    if (! empty($answers)) {
                        // Sort by order
                        asort($answers);
                        $lowestOrder = Arr::first($answers);
                        // Ensure it's numeric. Never leave anything to chance
                        $lowestOrder = is_numeric($lowestOrder) ? $lowestOrder : 0;

                        // We grab the lowest order. If this is "dubbelglas" or worse, we need to add it to to-do
                        // We don't need to check the second value. If the value is better than "dubbelglas", then
                        // it's complete
                        $category = $lowestOrder > 1 ? static::CATEGORY_COMPLETE : static::CATEGORY_TO_DO;
                    }
                    break;

                case 'crack-sealing':
                    $relevantQuestion = ToolQuestion::findByShort('crack-sealing-type');
                    if ($relevantQuestion instanceof ToolQuestion) {
                        $answer = $building->getAnswer($masterInputSource, $relevantQuestion);
                        $elementValue = ElementValue::find($answer);

                        if ($elementValue instanceof ElementValue) {
                            // If available, it's complete. Calculate value 1 and 2 are "ja".
                            $category = $elementValue->calculate_value > 2 ? static::CATEGORY_TO_DO
                                : static::CATEGORY_COMPLETE;
                        }
                    }
                    break;

                case 'hr-boiler':
                    // We first need to check if HR-boiler has been selected as option
                    $hasBoilerQuestion = ToolQuestion::findByShort('heat-source');
                    if ($hasBoilerQuestion instanceof ToolQuestion) {
                        $answer = $building->getAnswer($masterInputSource, $hasBoilerQuestion);
                        if (is_array($answer) && in_array('hr-boiler', $answer)) {
                            // The user has a boiler, let's see if there's an age for it
                            $ageQuestion = ToolQuestion::findByShort('boiler-placed-date');
                            if ($ageQuestion instanceof ToolQuestion) {
                                $answer = $building->getAnswer($masterInputSource, $ageQuestion);

                                if (is_numeric($answer)) {
                                    $diff = now()->format('Y') - $answer;
                                    // If it's not 10 years old, it's complete
                                    // If it's between 10 and 13, it's later
                                    // If it's older than 13 years, it's to-do
                                    $category = $diff < 10 ? static::CATEGORY_COMPLETE
                                        : ($diff >= 13 ? static::CATEGORY_TO_DO : static::CATEGORY_LATER);
                                } else {
                                    // No placing date available. We will assume it's fine
                                    $category = static::CATEGORY_COMPLETE;
                                }
                            }
                        }
                    }
                    break;

                case 'sun-boiler':
                    $hasSunBoilerQuestion = ToolQuestion::findByShort('heater-type');
                    if ($hasSunBoilerQuestion instanceof ToolQuestion) {
                        $answer = $building->getAnswer($masterInputSource, $hasSunBoilerQuestion);
                        $serviceValue = ServiceValue::find($answer);

                        if ($serviceValue instanceof ServiceValue) {
                            // If the value is 1 (geen), we want it in to-do
                            $category = $serviceValue->calculate_value > 1 ? static::CATEGORY_COMPLETE
                                : static::CATEGORY_TO_DO;
                        }
                    }
                    break;

                case 'solar-panels':
                    // We first need to check if the user has solar panels
                    $hasPanelsQuestion = ToolQuestion::findByShort('has-solar-panels');
                    if ($hasPanelsQuestion instanceof ToolQuestion) {
                        $answer = $building->getAnswer($masterInputSource, $hasPanelsQuestion);
                        $toolQuestionCustomValue = $hasPanelsQuestion->toolQuestionCustomValues()
                            ->where('short', $answer)
                            ->first();

                        if ($toolQuestionCustomValue instanceof ToolQuestionCustomValue) {
                            if ($toolQuestionCustomValue->short === 'no') {
                                // No panels
                                $category = static::CATEGORY_TO_DO;
                            } else {
                                // The user has solar panels, let's see if there's an age for it
                                $ageQuestion = ToolQuestion::findByShort('solar-panels-placed-date');
                                if ($ageQuestion instanceof ToolQuestion) {
                                    $answer = $building->getAnswer($masterInputSource, $ageQuestion);

                                    if (is_numeric($answer)) {
                                        $diff = now()->format('Y') - $answer;

                                        // If it's not 25 years old, it's complete
                                        // Else it's to-do
                                        $category = $diff < 25 ? static::CATEGORY_COMPLETE : static::CATEGORY_TO_DO;
                                    } else {
                                        // No placing date available. We will assume it's fine
                                        $category = static::CATEGORY_COMPLETE;
                                    }
                                }
                            }
                        }
                    }
                    break;

                case 'ventilation':
                    $relevantQuestion = ToolQuestion::findByShort('ventilation-type');
                    if ($relevantQuestion instanceof ToolQuestion) {
                        $answer = $building->getAnswer($masterInputSource, $relevantQuestion);
                        $serviceValue = ServiceValue::find($answer);

                        if ($serviceValue instanceof ServiceValue) {
                            // Logic for ventilation is based on the type.
                            switch ($serviceValue->calculate_value) {
                                case 1:
                                    // Natural ventilation, always to do
                                    $category = static::CATEGORY_TO_DO;
                                    break;

                                case 2:
                                    // Mechanical ventilation, only has one measure (demand-driven)
                                    // We check the current demand-driven logic. If it's true, it's complete
                                    // (Logically then the measure won't be added, but we still want to categorize
                                    // as properly as possible)
                                    $demandDrivenQuestion = ToolQuestion::findByShort('ventilation-demand-driven');
                                    if ($demandDrivenQuestion instanceof ToolQuestion) {
                                        $answer = $building->getAnswer($masterInputSource, $demandDrivenQuestion);
                                        // False and null and 0 are empty. We check if it's empty, because then we
                                        // assume it's no
                                        $category = empty($answer) ? static::CATEGORY_TO_DO : static::CATEGORY_COMPLETE;
                                    }
                                    break;

                                case 3:
                                case 4:
                                    // Balanced and decentral ventilation have a measure for heat recovery
                                    // We can apply the same logic
                                    $heatRecoveryQuestion = ToolQuestion::findByShort('ventilation-heat-recovery');
                                    if ($heatRecoveryQuestion instanceof ToolQuestion) {
                                        $answer = $building->getAnswer($masterInputSource, $heatRecoveryQuestion);
                                        // False and null and 0 are empty. We check if it's empty, because then we
                                        // assume it's no
                                        $category = empty($answer) ? static::CATEGORY_TO_DO : static::CATEGORY_COMPLETE;
                                    }
                                    break;
                            }
                        }
                    }
                    break;
            }
        }

        // If the category is empty, we just add it to to-do. This is the safest bet
        $category = empty($category) ? static::CATEGORY_TO_DO : $category;

        Log::debug("Mapped category {$category} for measure application {$measureApplication->short}");
        return $category;
    }

    public static function getComfortForBuilding(Building $building): int
    {
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $serviceOrElementToMeasure = [
            'sun-boiler' => [
                'heater-place-replace',
            ],
            'hr-boiler' => [
                'high-efficiency-boiler-replace',
            ],
            'house-ventilation' => [
                'ventilation-balanced-wtw',
                'ventilation-decentral-wtw',
                'ventilation-demand-driven',
            ],
            'wall-insulation' => [
                'cavity-wall-insulation',
                'facade-wall-insulation',
                'wall-insulation-research',
            ],
            'floor-insulation' => [
                'floor-insulation',
                'bottom-insulation',
                'floor-insulation-research',
            ],
            'roof-insulation' => [
                'roof-insulation-pitched-inside',
                'roof-insulation-pitched-replace-tiles',
                'roof-insulation-flat-current',
                'roof-insulation-flat-replace-current',
            ],
            'crack-sealing' => [
                'crack-sealing',
            ],
            'glass' => [
                'glass-in-lead',
                'hrpp-glass-only',
                'hrpp-glass-frames',
                'hr3p-frames',
            ]
        ];

        $comfort = 0;
        $advicesForComfort = UserActionPlanAdvice::forUser($building->user)
            ->forInputSource($masterInputSource)
            ->whereHasMorph('userActionPlanAdvisable', [MeasureApplication::class], function (Builder $query) {
                // TODO: Currently maintenance has comfort 0. Check if we should evaluate it or not
                $query->where('measure_type', MeasureApplication::ENERGY_SAVING);
            })
            ->category(static::CATEGORY_TO_DO)
            ->get()->pluck('userActionPlanAdvisable');

        $buildingServices = BuildingService::forBuilding($building)
            ->forInputSource($masterInputSource)
            ->get();
        foreach ($buildingServices as $buildingService) {
            $serviceValue = $buildingService->serviceValue;
            if ($serviceValue instanceof ServiceValue && $serviceValue->service instanceof Service) {
                $measureShorts = $serviceOrElementToMeasure[$serviceValue->service->short] ?? [];
                $measure = $advicesForComfort->whereIn('short', $measureShorts)
                    ->first();

                $comfort += $measure instanceof MeasureApplication ? ($measure->configurations['comfort'] ?? 0)
                    : ($serviceValue->configurations['comfort'] ?? 0);
            } elseif (($service = $buildingService->service) instanceof Service && $service->short === 'total-sun-panels') {
                // Solar panels don't have service values. We just check if there's a measure to replace solar panels
                if (($measure = $advicesForComfort->where('short', 'solar-panels-place-replace')->first()) instanceof MeasureApplication) {
                   $comfort += $measure->configurations['comfort'] ?? 0;
                }
            }
        }

        $buildingElements = BuildingElement::forBuilding($building)
            ->forInputSource($masterInputSource)
            ->whereHas('element', function (Builder $query) {
                $query->whereNotIn('short', ['living-rooms-window', 'sleeping-rooms-window']);
            })
            ->get();
        foreach ($buildingElements as $buildingElement) {
            $elementValue = $buildingElement->elementValue;
            if ($elementValue instanceof ElementValue && $elementValue->element instanceof Element) {
                $measureShorts = $serviceOrElementToMeasure[$elementValue->element->short] ?? [];
                $measure = $advicesForComfort->whereIn('short', $measureShorts)
                    ->first();

                $comfort += $measure instanceof MeasureApplication ? ($measure->configurations['comfort'] ?? 0)
                    : ($elementValue->configurations['comfort'] ?? 0);
            }
        }

        $glassBuildingElements = BuildingElement::forBuilding($building)
            ->forInputSource($masterInputSource)
            ->whereHas('element', function (Builder $query) {
                $query->whereIn('short', ['living-rooms-windows', 'sleeping-rooms-windows']);
            })
            ->get();
        $glassMeasure = $advicesForComfort->whereIn('short', $serviceOrElementToMeasure['glass'])->first();
        if ($glassMeasure instanceof MeasureApplication) {
            $comfort += $glassMeasure->configurations['comfort'] ?? 0;
        } else {
            $glassComfort = 0;

            // TODO: Check this logic
            foreach ($glassBuildingElements as $buildingElement) {
                $elementValue = $buildingElement->elementValue;
                if ($elementValue instanceof ElementValue && $elementValue->element instanceof Element) {
                    $math = $elementValue->element->short === 'living-rooms-windows' ? 0.6 : 0.4;
                    $glassComfort += ($elementValue->configurations['comfort'] ?? 0) * $math;
                }
            }
            // Properly round
            $glassComfort = NumberFormatter::round($glassComfort, 1);

            $comfort += $glassComfort;
        }
        return $comfort;
    }
}
