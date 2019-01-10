<?php

namespace App\Helpers;

use App\Models\Cooperation;
use App\Models\Questionnaire;
use App\Models\Step;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;

class StepHelper
{
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
                1,
                2,
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
     * Check is a user is interested in a step.
     *
     * @param Step $step
     *
     * @return bool
     */
    public static function hasInterestInStep(Step $step): bool
    {
        if (array_key_exists($step->slug, self::STEP_INTERESTS)) {
            foreach (self::STEP_INTERESTS[$step->slug] as $type => $interestedIn) {
                if (\Auth::user()->isInterestedInStep($type, $interestedIn)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Get the next step for a user where the user shows interest in.
     *
     * @param Step $current
     *
     * @return array
     */
    public static function getNextStep(Step $current, Questionnaire $currentQuestionnaire = null): array
    {
        // get all the steps
        $steps = Cooperation::find(HoomdossierSession::getCooperation())->getActiveOrderedSteps();
        // create new collection for the completed steps
        $completedSteps = collect();

        $currentFound = false;

        // before we check for other pets we want to check if the current step has additional questionnaires
        // if it does and the user did not finish those we redirect to that tab
        if ($current->hasQuestionnaires()) {
            // get the questionnaires for the current step  & the user his completed questionnaires
            $questionnairesForCurrentStep =  collect($current->questionnaires);

            // we reject the current questionnaire from the questionnaires for the current step collection
            $remainingQuestionnaires = $questionnairesForCurrentStep->reject(function ($questionnairesForCurrentStep) use ($currentQuestionnaire) {
                return $questionnairesForCurrentStep->id == $currentQuestionnaire->id;
            });


            // the next questionnaire will be the one with the highest id,
            $questionnaireWithHighestId = $remainingQuestionnaires->max('id');


            $nextQuestionnaire = Questionnaire::find($questionnaireWithHighestId);








            // just keep it in case if it needs to be added back.
//            $userCompletedQuestionnaires = \Auth::user()->completedQuestionnaires;
//            // now get the non completed questionnaires for this step & user
//            $nonCompletedQuestionnairesForCurrentStep = $questionnairesForCurrentStep->filter(function ($questionnaire) use ($userCompletedQuestionnaires) {
//                return ! $userCompletedQuestionnaires->find($questionnaire) instanceof Questionnaire;
//            });

            // we should not take the first one i guess, should be on order based, but there is no order in the questionnaire table
            $nextQuestionnaire = $questionnairesForCurrentStep->next();

            // and return it with the tab id
            if ($nextQuestionnaire instanceof Questionnaire) {
                return ['route' => 'cooperation.tool.'.$current->slug.'.index', 'tab_id' => 'questionnaire-'.$nextQuestionnaire->id];
            }
        }

        // the step does not have custom questionnaires or the user does not have uncompleted questionnaires left for that step.
        // so we will redirect them to a next step.

        // remove the completed steps from the steps
        foreach ($steps as $step) {
            if ($step->id != $current->id && ! $currentFound) {
                $completedStep = $steps->search(function ($item) use ($step) {
                    return $item->id == $step->id;
                });

                $completedSteps->push($steps->pull($completedStep));
            } elseif ($step->id == $current->id) {
                $currentFound = true;

                $completedStep = $steps->search(function ($item) use ($step) {
                    return $item->id == $step->id;
                });

                $completedSteps->push($steps->pull($completedStep));
            }
        }

        // since we pulled the completed steps of the collection
        $nonCompletedSteps = $steps;
        // check if a user is interested
        // and if so return the route name
        foreach ($nonCompletedSteps as $nonCompletedStep) {
            if (self::hasInterestInStep($nonCompletedStep)) {
                $routeName = 'cooperation.tool.'.$nonCompletedStep->slug.'.index';

                return ['route' => $routeName, 'tab_id' => ''];
            }
        }

        // if the user has no steps left where they do not have any interest in, redirect them to their plan
        return ['route' => 'cooperation.tool.my-plan.index', 'tab_id' => ''];
    }
}
