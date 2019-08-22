<?php

namespace App\Helpers;

use App\Models\Interest;
use App\Models\Step;
use App\Models\UserActionPlanAdvice;
use App\Models\UserInterest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MyPlanHelper
{
    const STEP_INTERESTS = [
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
        'ventilation-information' => [
            'service' => [
                6,
            ],
        ],
    ];

    /**
     * Check is a user is interested in a measure.
     *
     * @param Step $step
     *
     * @return bool
     */
    public static function isUserInterestedInMeasure(Step $step): bool
    {
        $building = HoomdossierSession::getBuilding(true);
        $buildingOwner = $building->user;

        foreach (self::STEP_INTERESTS[$step->slug] as $type => $interestedIn) {
            if ($buildingOwner->getInterestedType($type, $interestedIn) instanceof UserInterest && $buildingOwner->isInterestedInStep($type, $interestedIn)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Save a user's interests from the my plan page.
     *
     * @param Request              $request
     * @param UserActionPlanAdvice $advice
     *
     * @return array
     */
    public static function saveUserInterests(Request $request, UserActionPlanAdvice $advice)
    {
        $adviceId = $advice->id;

        $myAdvice = $request->input('advice.'.$adviceId);

        $step = key($myAdvice);

        // the planned year input
        $requestPlannedYear = null;
        // the interested checkbox, which fills the planned column in the table
        $interested = false;

        // get the type of the step
//        $type = key(self::STEP_INTERESTS[$step]);
        // count the total interestedInIds
//        $totalInterestInIds = count(self::STEP_INTERESTS[$step][$type]);
        // get the full user input for the current step
//        $fullRequestForUserHisAdvicesOnCurrentStep = $request->input('advice.*.'.$step);

        if (array_key_exists('planned_year', $myAdvice[$step])) {
            $requestPlannedYear = $myAdvice[$step]['planned_year'];
        }

        if (array_key_exists('interested', $myAdvice[$step])) {
            $interested = true;
        }

//        $stepInterests = self::STEP_INTERESTS[$step];
        // update the planned year
        $updates = [
            'planned' => $interested,
            'planned_year' => $requestPlannedYear,
        ];

        // update the advices
        $advice->update($updates);

        /*
        $plannedYearsForCurrentStep = collect();
        // check if the current step has more then 1 interest question
        if ($totalInterestInIds > 1) {
            // get one specific advice for this current step
            foreach ($fullRequestForUserHisAdvicesOnCurrentStep as $fullRequestForUserHisAdviceOnCurrentStep) {
                // check if it is a array
                if (is_array($fullRequestForUserHisAdviceOnCurrentStep)) {
                    if ('energy_saving' == $fullRequestForUserHisAdviceOnCurrentStep['measure_type']) {
                        $plannedYearsForCurrentStep->push($fullRequestForUserHisAdviceOnCurrentStep['planned_year']);

                        // if the array key interested exists in one of the advice and the measure type is energy saving, set the interested level to true
                        // so even if there is only one checkbox checked, the interested level is true.
                        if (array_key_exists('interested', $fullRequestForUserHisAdviceOnCurrentStep) && 'energy_saving' == $fullRequestForUserHisAdviceOnCurrentStep['measure_type']) {
                            $interested = true;
                        }
                    }
                }
            }
        }

        // Get all the user's inputs for a specific advice
        $currentUserInputForHisAdvice = $request->input('advice.'.$adviceId.'.'.$step);
        // we only want to update the interest level if the measure type = energy saving and
        // if the user checked the interested box
        // so we check if the interested key is set in the current user input advice
        if ('energy_saving' == $advice->measureApplication->measure_type) {
            // get the planned year and current year
            $plannedYear = Carbon::create($requestPlannedYear);
            $currentYear = Carbon::now()->year(date('Y'));


            // check if the current step has more then 1 interest question
            // for those, we DON'T want to change the interested level based on the planned year if the interest box is not checked
            // but if the interest box is checked and the planned year is null, we change the interest level
            if ($totalInterestInIds > 1) {
                \Log::debug('There are multiple interested ids');
                // we collected all the planned years for the current step / stepmeasures
                // we always want to calculate with the lowest year possible.
                $plannedYear = Carbon::create($plannedYearsForCurrentStep->min());

                // if the user has no checkboxes checked
                if (! $interested) {
                    // if not interested, put the interest ID on
                    $interest = Interest::where('calculate_value', '=', 4)->first();
                } elseif (null != $requestPlannedYear && array_key_exists('interested', $currentUserInputForHisAdvice)) {
                    // change the value of the interested level based on the planned year

                    // If the filled in year has a difference of 3 years or less with
                    // the current year, we set the interest to 1 (Ja, op korte termijn)
                    if ($currentYear->diff($plannedYear)->y <= 3) {
                        $interest = Interest::where('calculate_value', '=', 1)->first();
                    } else {
                        // If the filled in year has a difference of more than 3 years than
                        // the current year, we set the interest  to 2 (Ja, op termijn)
                        $interest = Interest::where('calculate_value', '=', 2)->first();
                    }
                } elseif (array_key_exists('interested', $currentUserInputForHisAdvice)) {
                    // So the planned year is empty. Let's look for the advised year.
                    if (is_null($advice->year)) {
                        $advice->year = $advice->getAdviceYear();
                    }
                    if (! is_null($advice->year) && $currentYear->diff(Carbon::create($advice->year))->y <= 3) {
                        // If there's an adviced year and it's between now and three years, set it to 1 (Ja, op korte termijn)
                        $interest = Interest::where('calculate_value', '=', 1)->first();
                    }
                    if (is_null($advice->year)) {
                        // if there is no advice year available, we set the interest level to 3 (Misschien, meer informatie gewenst)
                        $interest = Interest::where('calculate_value', '=', 3)->first();
                    }
                    // last resort
                    if (! isset($interest)) {
                        // interested, but we know NOTHING about years, set to 2 (Ja, op termijn)
                        $interest = Interest::where('calculate_value', '=', 2)->first();
                    }
                }
            } else {
                // for the advices where users can only select 1 interest for a step
                if (! $interested) {
                    // if not interested, put the interest ID on
                    $interest = Interest::where('calculate_value', '=', 4)->first();
                } elseif (null != $requestPlannedYear) {
                    // change the value of the interested level based on the planned year

                    // If the filled in year has a difference of 3 years or less with
                    // the current year, we set the interest to 1 (Ja, op korte termijn)
                    if ($currentYear->diff($plannedYear)->y <= 3) {
                        $interest = Interest::where('calculate_value', '=', 1)->first();
                    } else {

                        // If the filled in year has a difference of more than 3 years than
                        // the current year, we set the interest  to 2 (Ja, op termijn)
                        $interest = Interest::where('calculate_value', '=', 2)->first();
                    }
                } else {

                    // So the planned year is empty. Let's look for the advised year.
                    if (is_null($advice->year)) {
                        $advice->year = $advice->getAdviceYear();
                    }

                    if (!is_null($advice->year) && $currentYear->diff(Carbon::create($advice->year))->y <= 3) {
                        // If there's an adviced year and it's between now and three years, set it to 1 (Ja, op korte termijn)
                        $interest = Interest::where('calculate_value', '=', 1)->first();
                    }
                    if (is_null($advice->year)) {
                        // if there is no advice year available, we set the interest level to 3 (Misschien, meer informatie gewenst)
                        $interest = Interest::where('calculate_value', '=', 3)->first();
                    }

                    // last resort
                    if (! isset($interest)) {
                        \Log::debug('$interest is not set, so the interest level is not determined yet.');
                        // interested, but we know NOTHING about years, set to 2 (Ja, op termijn)
                        $interest = Interest::where('calculate_value', '=', 2)->first();
                    }
                }
            }

            if (isset($interest)) {
                // Finally save the user's interests for the element or service
                foreach ($stepInterests as $type => $interestInIds) {
                    foreach ($interestInIds as $interestInId) {
                        UserInterest::updateOrCreate(
                            [
                                'user_id' => $advice->user->id,
                                'interested_in_type' => $type,
                                'interested_in_id' => $interestInId,
                            ],
                            [
                                'interest_id' => $interest->id,
                            ]
                        );
                    }
                }
            }

            // Now insulated glazing is a different case than the others:
            // We do have an invisible interested field for the step itself,
            // but we also have to set the indvidual measure application
            // interests
            if ('insulated-glazing' == $advice->step->slug) {
                \Log::debug('Updating the interest of measure application '.$advice->measureApplication->measure_name.' to '.$interest->name);
                UserInterest::updateOrCreate(
                    [
                        'user_id' => $advice->user->id,
                        'interested_in_type' => 'measure_application',
                        'interested_in_id' => $advice->measureApplication->id,
                    ],
                    [
                        'interest_id' => $interest->id,
                    ]
                );
            }
        }
        */

        return $step;
    }
}
