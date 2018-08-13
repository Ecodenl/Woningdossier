<?php

namespace App\Helpers;


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
                '3',
            ]
        ],
        'insulated-glazing' => [
            'element' => [
                '1',
                '2'
            ],
        ],
        'floor-insulation' => [
            'element' => [
                '4'
            ]
        ],
        'roof-insulation' => [
            'element' => [
                '5'
            ]
        ],
        'high-efficiency-boiler' => [
            'service' => [
                '4'
            ]
        ],
        'heat-pump' => [
            'service' => [
                '1',
                '2'
            ]
        ],
        'solar-panels' => [
            'service' => [
                '7'
            ]
        ],
        'heater' => [
            'service' => [
                '3'
            ]
        ],
        'ventilation-information' => [
            'service' => [
                '6'
            ]
        ],
    ];
    

    /**
     * Check if a user is interested in a measure
     *
     * @param $step
     * @return bool
     */
    public static function isUserInterestedInMeasure($step) : bool
    {

        foreach (self::STEP_INTERESTS[$step] as $type => $interestedIn) {
            if (\Auth::user()->getInterestedType($type, $interestedIn) instanceof UserInterest && \Auth::user()->isInterestedInStep($type, $interestedIn)) {
                return true;
            }
        }
    }


    /**
     * Save a user his interests from the my plan page
     *
     * @param Request $request
     * @param UserActionPlanAdvice $advice
     * @return string
     */
    public static function saveUserInterests(Request $request, UserActionPlanAdvice $advice) : string
    {
        $adviceId = $advice->id;

        $myAdvice = $request->input('advice.' . $adviceId);

        // if the user checked the interested button
        $step = key($myAdvice);
        $requestPlannedYear = array_shift($myAdvice[$step]);
        $stepInterests = MyPlanHelper::STEP_INTERESTS[$step];

        // update the planned year
        $updates = [
            'planned_year' => isset($requestPlannedYear) ? $requestPlannedYear : null
        ];

        $advice->update($updates);

        // get the planned year and current year
        $plannedYear = Carbon::create($requestPlannedYear);
        $currentYear = Carbon::now()->year(date('Y'));

        // check if the user set the planned year
        if ($requestPlannedYear != null) {

            // if the filled in year has a difference of 3 years lower then the current year
            // we set the interest id to 2 or ja op termijn
            if ($currentYear->diff($plannedYear)->y >= 3) {
                $interestId = 2;
            }
            // if the filled in year has a difference of 3 years higher then the current year
            // we set the interest id to 1 or yes in short term
            else if ($currentYear->diff($plannedYear)->y <= 3) {
                $interestId = 1;
            }

            // save the user his interests
            foreach ($stepInterests as $type => $interestInIds) {
                foreach ($interestInIds as $interestInId) {
                    UserInterest::updateOrCreate(
                        [
                            'interested_in_type' => $type,
                            'interested_in_id' => $interestInId
                        ],
                        [
                            'interest_id' => $interestId
                        ]
                    );
                }
            }
        }

        // and return the step slug
        return $step;
    }
}