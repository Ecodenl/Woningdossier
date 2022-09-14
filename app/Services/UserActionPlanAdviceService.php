<?php

namespace App\Services;

use App\Helpers\Arr;
use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\Cooperation\Tool\HeatPumpHelper;
use App\Helpers\StepHelper;
use App\Models\Building;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\RoofType;
use App\Models\ServiceValue;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use Illuminate\Database\Eloquent\Collection;
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
     * @param \App\Models\User $user
     * @param \App\Models\InputSource $inputSource
     * @param \App\Models\Step $step
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function clearForStep(User $user, InputSource $inputSource, Step $step): Collection
    {
        // so this is kind of a weird one, we have to clear the advices for the given input source
        // BUT also for the master.

        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        // Get old advices
        $oldAdvices = UserActionPlanAdvice::forUser($user)
            ->forInputSource($masterInputSource)
            ->forStep($step)
            ->withInvisible()
            ->get();

        // now delete the old advices, the one for the given input source and the master source.
        UserActionPlanAdvice::forUser($user)
            ->forInputSource($masterInputSource)
            ->forStep($step)
            ->withInvisible()
            ->delete();

        UserActionPlanAdvice::forUser($user)
            ->forInputSource($inputSource)
            ->forStep($step)
            ->withInvisible()
            ->delete();

        return $oldAdvices;
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
                    $advice->year = 0;
                }

                // check if we have to set the $savingsMoney to ntb.
                if ('energy_saving' == $advice->userActionPlanAdvisable->measure_type) {
                    $advice->savings_money = self::checkSavingsMoney($advice, $advice->savings_money);
                }

                // if advices are not desirable and the measureApplication is not an advice it will be added to the result
                if (!$withAdvices && !$measureApplication->isAdvice()) {
                    $result[$measureApplication->measure_type][$advice->step->slug][$measureApplication->short] = $advice;
                }

                // if advices are desirable we always add it.
                if ($withAdvices) {
                    $result[$measureApplication->measure_type][$advice->step->slug][$measureApplication->short] = $advice;
                }
            }
        }

        ksort($result);

        return $result;
    }

    /**
     * Set properties from old advices on another advice
     *
     * @param \App\Models\UserActionPlanAdvice $userActionPlanAdvice
     * @param \App\Models\MeasureApplication $measureApplication
     * @param \Illuminate\Database\Eloquent\Collection $oldAdvices
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
     * @param \App\Models\UserActionPlanAdvice $userActionPlanAdvice
     */
    public static function setAdviceVisibility(UserActionPlanAdvice $userActionPlanAdvice)
    {
        $building = $userActionPlanAdvice->user->building;
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        // It's always going to be visible by default. Further logic follows.
        $visible = true;

        $advisable = $userActionPlanAdvice->userActionPlanAdvisable;

        if ($advisable instanceof MeasureApplication) {
            if ($advisable->measure_type === MeasureApplication::MAINTENANCE) {
                // If it's maintenance, change logic. We never show maintenance in the quickscan, only in expert
                $visible = false;

                if ($building instanceof Building && $building->hasAnsweredExpertQuestion()) {
                    // Building has answered an expert question, so it's visible, with 2 exceptions...
                    $visible = true;

                    $shorts = ['replace-tiles', 'replace-roof-insulation'];

                    // Logic is simple for these 2 exceptions. If it's within 5 years, then we _do_ show it
                    if (in_array($advisable->short, $shorts) && ! is_null($userActionPlanAdvice->year)) {
                        $visible = $userActionPlanAdvice->year - now()->format('Y') <= 5;
                    }
                }
            } elseif ($advisable->measure_type === MeasureApplication::ENERGY_SAVING) {
                switch ($advisable->short) {
                    case 'high-efficiency-boiler-replace':
                        $subStep = SubStep::bySlug('warmtepomp')->first();
                        $evaluation = ConditionEvaluator::init()
                            ->building($building)
                            ->inputSource($masterInputSource)
                            ->evaluate($subStep->conditions);

                        if ($evaluation) {
                            // We hide the HR-boiler if the user has a full heat pump
                            $type = ServiceValue::find(
                                $building->getAnswer($masterInputSource, ToolQuestion::findByShort('heat-pump-type'))
                            );

                            if ($type instanceof ServiceValue && $type->calculate_value > 3) {
                                $visible = false;
                            }
                        }
                        break;
                }
            }
        }

        $userActionPlanAdvice->visible = $visible;
    }

    /**
     * Set the category of a user action plan advice
     *
     * @param \App\Models\UserActionPlanAdvice $userActionPlanAdvice
     */
    public static function setAdviceCategory(UserActionPlanAdvice $userActionPlanAdvice)
    {
        // The logic for a category is a bit more complex than visibility. We define to-do as default
        $category = static::CATEGORY_TO_DO;

        $advisable = $userActionPlanAdvice->userActionPlanAdvisable;

        if ($advisable instanceof MeasureApplication) {
            $building = $userActionPlanAdvice->user->building;

            // Chance of a building not being set is small, but not impossible!
            if ($building instanceof Building) {
                $category = static::getCategoryFromMeasure($building, $advisable);
            }
        }

        $userActionPlanAdvice->category = $category;
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
                'roof-insulation-pitched-inside' => 'roof-insulation',
                'roof-insulation-pitched-replace-tiles' => 'roof-insulation',
                'roof-insulation-flat-current' => 'roof-insulation',
                'roof-insulation-flat-replace-current' => 'roof-insulation',
                'high-efficiency-boiler-replace' => 'hr-boiler',
                'heater-place-replace' => 'sun-boiler',
                'solar-panels-place-replace' => 'solar-panels',
                'ventilation-balanced-wtw' => 'ventilation',
                'ventilation-decentral-wtw' => 'ventilation',
                'ventilation-demand-driven' => 'ventilation',
                'hybrid-heat-pump-outside-air' => 'heat-pump',
                'hybrid-heat-pump-ventilation-air' => 'heat-pump',
                'hybrid-heat-pump-pvt-panels' => 'heat-pump',
                'full-heat-pump-outside-air' => 'heat-pump',
                'full-heat-pump-ground-heat' => 'heat-pump',
                'full-heat-pump-pvt-panels' => 'heat-pump',
                'heat-pump-boiler-place-replace' => 'heat-pump-boiler',
            ];

            $logicShort = $categorization[$measureApplication->short];
            switch ($logicShort) {
                case 'insulation':
                    // Multiple types of insulation, we check based on measure which question is relevant
                    $floorShorts = ['floor-insulation', 'bottom-insulation', 'floor-insulation-research'];

                    // Define tool question based on the measure short
                    $toolQuestionShort = in_array($measureApplication->short, $floorShorts) ? 'current-floor-insulation'
                        : 'current-wall-insulation';

                    $relevantQuestion = ToolQuestion::findByShort($toolQuestionShort);
                    $answer = $building->getAnswer($masterInputSource, $relevantQuestion);
                    $elementValue = ElementValue::find($answer);
                    if ($elementValue instanceof ElementValue) {
                        // If the value is 1 or 2 (onbekend, geen), we want it in to-do
                        // If it's "niet van toepassing" it should be hidden, so we don't worry about it
                        $category = $elementValue->calculate_value > 2 ? static::CATEGORY_COMPLETE
                            : static::CATEGORY_TO_DO;
                    }
                    break;

                case 'roof-insulation':
                    // Due to the roof types, we need custom logic
                    $roofStep = Step::findByShort('roof-insulation');
                    $elementValueId = null;

                    if ($building->hasAnsweredExpertQuestion($roofStep)) {
                        // The user has completed the roof insulation step, so we must check the insulation type
                        // for the roof types
                        $flatShorts = ['roof-insulation-flat-current', 'roof-insulation-flat-replace-current'];
                        // Check which roof to get
                        $roofTypeShort = in_array($measureApplication->short, $flatShorts) ? 'flat' : 'pitched';
                        $roofType = RoofType::findByShort($roofTypeShort);

                        // Get the related roof type
                        $buildingRoofType = $building->roofTypes()
                            ->forInputSource($masterInputSource)
                            ->where('roof_type_id', $roofType->id)
                            ->first();

                        $elementValueId = optional($buildingRoofType)->element_value_id;
                    } else {
                        // Still a quick-scan newbie, we check the tool question
                        $relevantQuestion = ToolQuestion::findByShort('current-roof-insulation');
                        $elementValueId = $building->getAnswer($masterInputSource, $relevantQuestion);
                    }

                    // Now we have the element value of the relevant roof type, so we set the category
                    $elementValue = ElementValue::find($elementValueId);
                    if ($elementValue instanceof ElementValue) {
                        // If the value is 1 or 2 (onbekend, geen), we want it in to-do
                        // If it's "niet van toepassing" it should be hidden, so we don't worry about it
                        $category = $elementValue->calculate_value > 2 ? static::CATEGORY_COMPLETE
                            : static::CATEGORY_TO_DO;
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
                    $answer = $building->getAnswer($masterInputSource, $relevantQuestion);
                    $elementValue = ElementValue::find($answer);

                    if ($elementValue instanceof ElementValue) {
                        // If available, it's complete. Calculate value 1 and 2 are "ja".
                        $category = $elementValue->calculate_value > 2 ? static::CATEGORY_TO_DO
                            : static::CATEGORY_COMPLETE;
                    }
                    break;

                case 'hr-boiler':
                    // We first need to check if the HR-boiler has been selected as option
                    $heatSourceQuestion = ToolQuestion::findByShort('heat-source');
                    $heatSourceWaterQuestion = ToolQuestion::findByShort('heat-source-warm-tap-water');
                    $answer = array_merge($building->getAnswer($masterInputSource, $heatSourceQuestion), $building->getAnswer($masterInputSource, $heatSourceWaterQuestion));
                    if (in_array('hr-boiler', $answer)) {
                        // The user has a boiler, let's see if there's an age for it
                        $ageQuestion = ToolQuestion::findByShort('boiler-placed-date');
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
                    break;

                case 'sun-boiler':
                    $heatSourceQuestion = ToolQuestion::findByShort('heat-source');
                    $heatSourceWaterQuestion = ToolQuestion::findByShort('heat-source-warm-tap-water');
                    $answer = array_merge($building->getAnswer($masterInputSource, $heatSourceQuestion), $building->getAnswer($masterInputSource, $heatSourceWaterQuestion));

                    // If they don't have a sun-boiler, we will put it in to-do
                    $category = in_array('sun-boiler', $answer) ? static::CATEGORY_COMPLETE
                        : static::CATEGORY_TO_DO;
                    break;

                case 'solar-panels':
                    // We first need to check if the user has solar panels
                    $hasPanelsQuestion = ToolQuestion::findByShort('has-solar-panels');
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
                    break;

                case 'ventilation':
                    $relevantQuestion = ToolQuestion::findByShort('ventilation-type');
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
                                $answer = $building->getAnswer($masterInputSource, $demandDrivenQuestion);
                                // False and null and 0 are empty. We check if it's empty, because then we
                                // assume it's no
                                $category = empty($answer) ? static::CATEGORY_TO_DO : static::CATEGORY_COMPLETE;
                                break;

                            case 3:
                            case 4:
                                // Balanced and decentral ventilation have a measure for heat recovery
                                // We can apply the same logic
                                $heatRecoveryQuestion = ToolQuestion::findByShort('ventilation-heat-recovery');
                                $answer = $building->getAnswer($masterInputSource, $heatRecoveryQuestion);
                                // False and null and 0 are empty. We check if it's empty, because then we
                                // assume it's no
                                $category = empty($answer) ? static::CATEGORY_TO_DO : static::CATEGORY_COMPLETE;
                                break;
                        }
                    }
                    break;

                case 'heat-pump':
                    $subStep = SubStep::bySlug('warmtepomp')->first();
                    $evaluation = ConditionEvaluator::init()
                        ->building($building)
                        ->inputSource($masterInputSource)
                        ->evaluate($subStep->conditions);
                    $type = ServiceValue::find(
                        $building->getAnswer($masterInputSource, ToolQuestion::findByShort('heat-pump-type'))
                    );

                    if ($evaluation && $type instanceof ServiceValue) {
                        $category = self::CATEGORY_COMPLETE;

                        $placeYear = ServiceValue::find($building->getAnswer($masterInputSource,
                            ToolQuestion::findByShort('heat-pump-placed-date')));

                        if (is_numeric($placeYear)) {
                            $diff = now()->format('Y') - $placeYear;

                            // Maintenance interval
                            if ($diff >= 18) {
                                $category = static::CATEGORY_TO_DO;
                            }
                        }
                    } else {
                        $category = static::CATEGORY_TO_DO;
                    }
                    break;

                case 'heat-pump-boiler':
                    // If it's been calculated, the user has selected it in either the new or the old situation, so
                    // we only need to check one of them because it's safe to assume the situation.
                    $category = in_array('heat-pump-boiler',
                        $building->getAnswer($masterInputSource, ToolQuestion::findByShort('heat-pump-type')))
                        ? static::CATEGORY_COMPLETE : static::CATEGORY_TO_DO;
                    break;
            }
        }

        // If the category is empty, we just add it to to-do. This is the safest bet
        $category = empty($category) ? static::CATEGORY_TO_DO : $category;

        Log::debug("Mapped category {$category} for measure application {$measureApplication->short}");
        return $category;
    }

    /**
     * Format cost array based on value of cost indication.
     *
     * @param $costIndication
     *
     * @return array|null[]
     */
    public static function formatCosts($costIndication): array
    {
        return [
            'from' => $costIndication <= 0 ? $costIndication : null,
            'to' => $costIndication > 0 ? $costIndication : null,
        ];
    }
}
