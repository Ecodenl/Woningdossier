<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\Interest;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Models\StepComment;
use App\Models\User;
use App\Models\CompletedStep;
use App\Models\UserInterest;
use Illuminate\Database\Query\Builder;

class StepHelper
{
    const ELEMENT_TO_SHORT = [
        'sleeping-rooms-windows' => 'insulated-glazing',
        'living-rooms-windows' => 'insulated-glazing',
        'crack-sealing' => 'insulated-glazing',
        'wall-insulation' => 'wall-insulation',
        'floor-insulation' => 'floor-insulation',
        'roof-insulation' => 'roof-insulation',
    ];
    const SERVICE_TO_SHORT = [
        'hr-boiler' => 'high-efficiency-boiler',
        'boiler' => 'high-efficiency-boiler',
        'total-sun-panels' => 'solar-panels',
        'sun-boiler' => 'heater',
        'house-ventilation' => 'ventilation'
    ];

    /**
     * Get alle the comments categorized under step and input source
     *
     * @param Building $building
     * @param bool $withEmptyComments
     * @param null $specificInputSource
     *
     * @return array
     */
    public static function getAllCommentsByStep(
        Building $building,
        $withEmptyComments = false,
        $specificInputSource = null
    ): array
    {
        $commentsByStep = [];

        if (!$building instanceof Building) {
            return [];
        }

        $stepComments = StepComment::forMe($building->user)->with('step',
            'inputSource')->get();

        // when set, we will only return the comments for the given input source
        if ($specificInputSource instanceof InputSource) {
            $stepComments = $stepComments->where('input_source_id',
                $specificInputSource->id);
        }

        foreach ($stepComments as $stepComment) {

            if ($stepComment->step->isSubStep()) {
                if (is_null($stepComment->short)) {
                    $commentsByStep[$stepComment->step->parentStep->short][$stepComment->step->short][$stepComment->inputSource->name] = $stepComment->comment;
                } else {
                    $commentsByStep[$stepComment->step->parentStep->short][$stepComment->step->short][$stepComment->inputSource->name][$stepComment->short] = $stepComment->comment;
                }
            } else {
                if (is_null($stepComment->short)) {
                    $commentsByStep[$stepComment->step->short]['-'][$stepComment->inputSource->name] = $stepComment->comment;
                }
            }
        }

        return $commentsByStep;
    }

    /**
     * Method to check whether a user has interest in a step
     *
     * @param User $user
     * @param InputSource $inputSource
     * @param $interestedInType
     * @param $interestedInId
     *
     * @return bool
     */
    public static function hasInterestInStep(User $user, $interestedInType, $interestedInId, $inputSource = null): bool
    {
        $noInterestIds = Interest::whereIn('calculate_value', [4, 5])->select('id')->get()->pluck('id')->toArray();

        $userSelectedInterestedId = null;
        if ($inputSource instanceof InputSource) {
            $userSelectedUserInterest = $user->userInterestsForSpecificType($interestedInType,
                $interestedInId, $inputSource)->first();
        } else {
            $userSelectedUserInterest = $user->userInterestsForSpecificType($interestedInType,
                $interestedInId)->first();
        }

        if ($userSelectedUserInterest instanceof UserInterest) {
            $userSelectedInterestedId = $userSelectedUserInterest->interest_id;
        }

        return !in_array($userSelectedInterestedId, $noInterestIds);
    }

    /**
     * Method to retrieve the unfinished sub steps of a step for a building
     *
     * @param Step $step
     * @param Building $building
     *
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public static function getUnfinishedSubStepsForStep(
        Step $step,
        Building $building,
        InputSource $inputSource
    )
    {
        return $step->subSteps()
            ->whereNotExists(function (Builder $query) use (
                $building,
                $inputSource
            ) {
                $query->select('*')
                    ->from('completed_steps')
                    ->where('completed_steps.input_source_id',
                        $inputSource->id)
                    ->whereRaw('steps.id = completed_steps.step_id')
                    ->where('building_id', $building->id);
            })->get();
    }

    /**
     * Get the next step for a user where the user shows interest in or the next questionnaire for a user.
     *
     * @param Building $building
     * @param InputSource $inputSource
     * @param Step $current
     * @param Questionnaire $currentQuestionnaire
     *
     * @return array
     */
    public static function getNextStep(
        Building $building,
        InputSource $inputSource,
        Step $current,
        Questionnaire $currentQuestionnaire = null
    ): array
    {
        /**
         * some default stuff set so we dont get if else in this method all over the place
         *
         * @var Step $parentStep
         * @var Step $subStep
         */
        $parentStep = $current->isSubStep() ? $current->parentStep : $current;
        $subStep = $current->isSubStep() ? $current : null;

        $url = static::buildStepUrl($parentStep, $subStep);

        // count all the active questionnaires for the current step
        $allActiveQuestionnairesForCurrentStepCount = $parentStep->questionnaires()->active()->count();
        $user = $building->user;

        $nonCompletedSteps = collect();
        // when there is a substep try to redirect them to the next sub step
        if ($subStep instanceof Step) {
            $nonCompletedSteps = static::getUnfinishedSubStepsForStep($parentStep,
                $building, $inputSource);
        }

        // there are no uncompleted sub steps for the parent, try to redirect them to a questionnaire that may exists on the parent step.
        if ($nonCompletedSteps->isEmpty() && $parentStep->hasQuestionnaires() && $allActiveQuestionnairesForCurrentStepCount > 0) {
            // before we check for other steps we want to check if the current step has active additional questionnaires
            // if it does and the user did not finish those we redirect to that tab
            // create the base query to obtain the non completed questionnaires for the current step.
            $nonCompletedSteps = $parentStep->questionnaires()
                ->active()
                ->whereNotExists(function (
                    Builder $query
                ) use ($user, $inputSource) {
                    $query->select('*')
                        ->from('completed_questionnaires')
                        ->where('completed_questionnaires.input_source_id',
                            $inputSource->id)
                        ->whereRaw('questionnaires.id = completed_questionnaires.questionnaire_id')
                        ->where('user_id',
                            $user->id);
                })->orderBy('order')->get();
        }

        // there are no uncompleted sub steps or uncompleted questionnaires left for this step.
        // so we will redirect them to the next step.
        // retrieve the non completed steps for a user.
        // we leave out the general data step itself since thats not a "real" step anymore
        if ($nonCompletedSteps->isEmpty()) {
            $nonCompletedSteps = $user->cooperation
                ->steps()
                ->where('steps.short', '!=', 'general-data')
                ->where('steps.parent_id', '=', null)
                ->orderBy('cooperation_steps.order')
                ->where('cooperation_steps.is_active', '1')
                ->whereNotExists(function (Builder $query) use (
                    $building,
                    $inputSource
                ) {
                    $query->select('*')
                        ->from('completed_steps')
                        ->whereRaw('steps.id = completed_steps.step_id')
                        ->where('building_id', $building->id)
                        ->where('input_source_id', $inputSource->id);
                })->get();
        }


        foreach ($nonCompletedSteps as $nonCompletedStep) {

            // when the non completed step is a substep, we can always return it.
            // else we have to check whether the user has interest in the step
            if ($nonCompletedStep instanceof Step && ($nonCompletedStep->isSubStep() || self::hasInterestInStep($user,
                        Step::class, $nonCompletedStep->id))) {

                // when its a substep we need to build it again for the sub step
                if ($nonCompletedStep->isSubStep()) {
                    $url = static::buildStepUrl($parentStep, $nonCompletedStep);
                } else {
                    $url = static::buildStepUrl($nonCompletedStep);
                }

                return ['url' => $url, 'tab_id' => ''];
            }

            if ($nonCompletedStep instanceof Questionnaire) {
                return [
                    'url' => $url,
                    'tab_id' => 'questionnaire-' . $nonCompletedStep->id
                ];
            }
        }

        // if the user has no steps left where they do not have any interest in, redirect them to their plan
        return [
            'url' => route('cooperation.tool.my-plan.index'), 'tab_id' => ''
        ];
    }

    /**
     * Return a step url
     *
     * @param Step $parentStep
     * @param null $subStep
     *
     * @return string
     */
    public static function buildStepUrl(Step $parentStep, $subStep = null): string
    {
        return route(
            $subStep instanceof Step ? 'cooperation.tool.' . $parentStep->short . '.' . $subStep->short . '.index' : 'cooperation.tool.' . $parentStep->short . '.index'
        );
    }

    /**
     * Complete a step for a building.
     *
     * @param Step $step
     * @param Building $building
     * @param InputSource $inputSource
     *
     */
    public static function complete(Step $step, Building $building, InputSource $inputSource)
    {
        CompletedStep::firstOrCreate([
            'step_id' => $step->id,
            'input_source_id' => $inputSource->id,
            'building_id' => $building->id,
        ]);

        // check if all sub steps are completed, if so complete the parent step
        if ($step->isSubStep()) {
            $parentStep = $step->parentStep;
            $uncompletedSubStepsForParentStep = $parentStep
                ->subSteps()
                ->whereNotExists(function (Builder $query) use ($building, $inputSource) {
                    $query->select('*')
                        ->from('completed_steps')
                        ->whereRaw('steps.id = completed_steps.step_id')
                        ->where('building_id', $building->id)
                        ->where('input_source_id', $inputSource->id);
                })->get();

            // when there are no uncompleted sub steps, we can complete the parent step.
            if ($uncompletedSubStepsForParentStep->isEmpty()) {
                CompletedStep::firstOrCreate([
                    'step_id' => $parentStep->id,
                    'input_source_id' => $inputSource->id,
                    'building_id' => $building->id,
                ]);
            }
        }

    }
}
