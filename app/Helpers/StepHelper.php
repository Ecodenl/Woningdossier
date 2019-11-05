<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\BuildingHeater;
use App\Models\BuildingPvPanel;
use App\Models\Element;
use App\Models\InputSource;
use App\Models\Questionnaire;
use App\Models\Service;
use App\Models\Step;
use App\Models\StepComment;
use App\Models\User;
use App\Models\UserActionPlanAdviceComments;
use App\Models\UserEnergyHabit;
use App\Models\CompletedStep;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Log;

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

    const STEP_INTERESTS = [
        'ventilation-information' => [
            'service' => [
                6,
            ],
        ],
        // step name
        'wall-insulation' => [
            // type
            'element' => [
                // interested in id (Element id, service id etc)
                3,
            ],
        ],
        'insulated-glazing' => [
            'element' => [
                1,
                2,
            ],
        ],
        'floor-insulation' => [
            'element' => [
                4,
            ],
        ],
        'roof-insulation' => [
            'element' => [
                5,
            ],
        ],
        'high-efficiency-boiler' => [
            'service' => [
                4,
            ],
        ],
        'heat-pump' => [
            'service' => [
                8
            ],
        ],
        'solar-panels' => [
            'service' => [
                7,
            ],
        ],
        'heater' => [
            'service' => [
                3,
            ],
        ],
    ];

    /**
     * Method to return a array of all comments, categorized under step and input source and more cats if needed.
     *
     * @param User $user
     * @param bool $withEmptyComments
     *
     * @note not used anymore, code can be used to remove the old stuff.
     * @return array
     */
    public static function getAllCommentsByStepOld(User $user, $withEmptyComments = false): array
    {
        $building = $user->building;

        if (!$building instanceof Building) {
            return [];
        }

        $allInputForMe = collect();
        $commentsByStep = [];


        /* General-data */
        $userEnergyHabitForMe = UserEnergyHabit::forMe($user)->with('inputSource')->get();
        $allInputForMe->put('general-data', $userEnergyHabitForMe);

        /* wall insulation */
        $buildingFeaturesForMe = BuildingFeature::forMe($user)->with('inputSource')->get();
        $allInputForMe->put('wall-insulation', $buildingFeaturesForMe);

        /* floor insualtion */
        $crawlspace = Element::where('short', 'crawlspace')->first();
        $buildingElementsForMe = BuildingElement::forMe($user)->with('inputSource')->get();
        $allInputForMe->put('floor-insulation', $buildingElementsForMe->where('element_id', $crawlspace->id));

        /* beglazing */
        $insulatedGlazingsForMe = $building->currentInsulatedGlazing()->forMe($user)->with('inputSource')->get();
        $allInputForMe->put('insulated-glazing', $insulatedGlazingsForMe);

        /* roof */
        $currentRoofTypesForMe = $building->roofTypes()->with('roofType')->forMe($user)->with('inputSource')->get();
        $allInputForMe->put('roof-insulation', $currentRoofTypesForMe);

        /* hr boiler ketel */
        $boiler = Service::where('short', 'boiler')->first();
        $installedBoilerForMe = $building->buildingServices()->forMe($user)->where('service_id', $boiler->id)->with('inputSource')->get();
        $allInputForMe->put('high-efficiency-boiler', $installedBoilerForMe);

        /* sun panel*/
        $buildingPvPanelForMe = BuildingPvPanel::forMe($user)->with('inputSource')->get();
        $allInputForMe->put('solar-panels', $buildingPvPanelForMe);

        /* heater */
        $buildingHeaterForMe = BuildingHeater::forMe($user)->with('inputSource')->get();
        $allInputForMe->put('heater', $buildingHeaterForMe);

        /* my plan */
//        $allInputForMe->put('my-plan', UserActionPlanAdviceComments::forMe($user)->get());

        // the attributes that can contain any sort of comments.
        $possibleAttributes = ['comment', 'additional_info', 'living_situation_extra'];

//        dd($allInputForMe);
        foreach ($allInputForMe as $step => $inputForMeByInputSource) {
            foreach ($inputForMeByInputSource as $inputForMe) {
                // check if we need the extra column to extract the comment from.
                if (is_array($inputForMe->extra) && array_key_exists('comment', $inputForMe->extra)) {
                    // get the comment fields, and filter out the empty ones.
                    $inputToFilter = $inputForMe->extra;
                } else {
                    $inputToFilter = $inputForMe->getAttributes();
                }

                $inputWithComments = \Illuminate\Support\Arr::only($inputToFilter, $possibleAttributes);

                $comments = array_values($withEmptyComments ? $inputWithComments : array_filter(
                    $inputWithComments
                ));

                // if the comments are not empty, add it to the array with its input source
                // only add the comment, not the key / column name.
                if (! empty($comments)) {
                    $commentsByStep[$step][$inputForMe->inputSource->name] = $comments[0];
                }
            }
        }

        return $commentsByStep;
    }

    public static function getAllCommentsByStep(User $user, $withEmptyComments = false): array
    {
        $building = $user->building;
        $commentsByStep = [];

        if (!$building instanceof Building) {
            return [];
        }

        $stepComments = StepComment::forMe($user)->with('step', 'inputSource')->get();

        foreach ($stepComments as $stepComment) {
            if (is_null($stepComment->short)) {
                $commentsByStep[$stepComment->step->short][$stepComment->inputSource->name] = $stepComment->comment;
            } else {
                $commentsByStep[$stepComment->step->short][$stepComment->inputSource->name][$stepComment->short] = $stepComment->comment;
            }
        }

        return $commentsByStep;
    }

    /**
     * Check is a user is interested in a step.
     *
     * @param Building    $building
     * @param InputSource $inputSource
     * @param Step        $step
     *
     * @return bool
     */
    public static function hasInterestInStep(Building $building, InputSource $inputSource, Step $step): bool
    {
        if (array_key_exists($step->slug, self::STEP_INTERESTS)) {
            foreach (self::STEP_INTERESTS[$step->slug] as $type => $interestedIn) {
                if ($building->isInterestedInStep($inputSource, $type, $interestedIn)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the next step for a user where the user shows interest in or the next questionnaire for a user.
     *
     * @param Building      $building
     * @param InputSource   $inputSource
     * @param Step          $current
     * @param Questionnaire $currentQuestionnaire
     *
     * @return array
     */
    public static function getNextStep(Building $building, InputSource $inputSource, Step $current, Questionnaire $currentQuestionnaire = null): array
    {
        // count all the active questionnaires for the current step
        $allActiveQuestionnairesForCurrentStepCount = $current->questionnaires()->active()->count();
        $user = $building->user;

        // before we check for other steps we want to check if the current step has active additional questionnaires
        // if it does and the user did not finish those we redirect to that tab
        if ($current->hasQuestionnaires() && $allActiveQuestionnairesForCurrentStepCount > 0) {
            // create the base query to obtain the non completed questionnaires for the current step.
            $nonCompletedQuestionnairesForCurrentStepQuery = $current->questionnaires()
                ->whereNotExists(function (Builder $query) use ($user) {
                    $query->select('*')
                        ->from('completed_questionnaires')
                        ->whereRaw('questionnaires.id = completed_questionnaires.questionnaire_id')
                        ->where('user_id', $user->id);
                })->active()
                ->orderBy('order');
            // since it can be null
            if ($currentQuestionnaire instanceof Questionnaire) {
                $nextQuestionnaire = $nonCompletedQuestionnairesForCurrentStepQuery
                    ->where('id', '!=', $currentQuestionnaire->id)
                    ->where('order', '>', $currentQuestionnaire->order)
                    ->first();
                // and return it with the tab id
                if ($nextQuestionnaire instanceof Questionnaire) {
                    return ['url' => route('cooperation.tool.'.$current->slug.'.index'), 'tab_id' => 'questionnaire-'.$nextQuestionnaire->id];
                }
            } else {
                // no need for extra queries.
                $nextQuestionnaire = $nonCompletedQuestionnairesForCurrentStepQuery->first();
                if ($nextQuestionnaire instanceof Questionnaire) {
                    return ['url' => route('cooperation.tool.'.$current->slug.'.index'), 'tab_id' => 'questionnaire-'.$nextQuestionnaire->id];
                }
            }
        }
        // the step does not have custom questionnaires or the user does not have uncompleted questionnaires left for that step.
        // so we will redirect them to a next step.
        // retrieve the non completed steps for a user.
        $nonCompletedSteps = $user->cooperation
            ->steps()
            ->orderBy('cooperation_steps.order')
            ->where('cooperation_steps.is_active', '1')
            ->whereNotExists(function (Builder $query) use ($building, $inputSource) {
                $query->select('*')
                    ->from('completed_steps')
                    ->whereRaw('steps.id = completed_steps.step_id')
                    ->where('building_id', $building->id)
                    ->where('input_source_id', $inputSource->id);
            })->get();
        // check if a user is interested
        // and if so return the route name
        foreach ($nonCompletedSteps as $nonCompletedStep) {
            if (self::hasInterestInStep($building, $inputSource, $nonCompletedStep)) {
                $routeName = 'cooperation.tool.'.$nonCompletedStep->slug.'.index';

                return ['url' => route($routeName), 'tab_id' => ''];
            }
        }
        // if the user has no steps left where they do not have any interest in, redirect them to their plan
        return ['url' => route('cooperation.tool.my-plan.index'), 'tab_id' => ''];
    }

    /**
     * Complete a step for a building.
     *
     * @param Step        $step
     * @param Building    $building
     * @param InputSource $inputSource
     *
     * @return Model|CompletedStep
     */
    public static function complete(Step $step, Building $building, InputSource $inputSource)
    {
        return CompletedStep::firstOrCreate([
            'step_id' => $step->id,
            //'input_source_id' => HoomdossierSession::getInputSource(),
            'input_source_id' => $inputSource->id,
            //'building_id' => HoomdossierSession::getBuilding(),
            'building_id' => $building->id,
        ]);
    }
}
