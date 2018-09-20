<?php

namespace App\Helpers;


use App\Models\Cooperation;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class StepHelper
{
    const STEP_INTERESTS = [
        'ventilation-information' => [
            'service' => [
                6
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
     * Check is a user is interested in a step
     *
     * @param Step $step
     * @return bool
     */
    public static function hasInterestInStep(Step $step) : bool
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
     * Get the next step for a user where the user shows interest in
     *
     * @return string
     */
    public static function getNextStep(Step $current) : string
    {
        // get all the steps
        $steps = Step::orderBy('order')->get();
        // create new collection for the completed steps
        $completedSteps = collect();

        $currentFound = false;

        // remove the completed steps from the steps
        foreach ($steps as $step) {
        	if ($step != $current && !$currentFound){
		        $completedStep = $steps->search(function ($item) use ($step) {
			        return $item->id == $step->id;
		        });

		        $completedSteps->push($steps->pull($completedStep));
	        }
	        elseif ($step == $current){
        		$currentFound = true;

		        $completedStep = $steps->search(function ($item) use ($step) {
			        return $item->id == $step->id;
		        });

		        $completedSteps->push($steps->pull($completedStep));
	        }

	        /*
            if (\Auth::user()->hasCompleted($step)) {
                // get the completed step
                // $completedStep is the index of the collection item, so we can pull it from the steps itself
                $completedStep = $steps->search(function ($item) use ($step) {
                    return $item->id == $step->id;
                });

                $completedSteps->push($steps->pull($completedStep));
            }
	        */
        }

        // since we pulled the completed steps of the collection
        $nonCompletedSteps = $steps;
        // check if a user is interested
        // and if so return the route name
        foreach ($nonCompletedSteps as $nonCompletedStep) {

            if (self::hasInterestInStep($nonCompletedStep)) {
                $routeName = "cooperation.tool." . $nonCompletedStep->slug . ".index";
                return $routeName;
            }
        }

        // if the user has no steps left where they do not have any interest in, redirect them to their plan
        return "cooperation.tool.my-plan.index";
    }
}