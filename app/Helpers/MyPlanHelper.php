<?php

namespace App\Helpers;


use App\Models\UserInterest;

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

    const INTERESTED_ID_BY_STEP = [
        // step name
        'wall-insulation' => [
            // interested in id (Element id, service id etc)
            '3' => 'element',
            '2' => 'element',
        ],
        'insulated-glazing' => [
            '1' => 'element',
        ],
        'floor-insulation' => [
            '4' => 'element'
        ],
        'roof-insulation' => [
            '5' => 'element'
        ],
        'high-efficiency-boiler' => [
            '4' => 'service'
        ],
        'heat-pump' => [
            '1' => 'service',
            '2' => 'service'
        ],
        'solar-panels' => [
            '7' => 'service'
        ],
        'heater' => [
            '3' => 'service'
        ],
        'ventilation-information' => [
            '6' => 'service'
        ],
    ];

    /**
     * Check if a user is interested in a measure
     *
     * @param $step
     * @return bool
     */
    public static function isUserInterestedInMeasure($step)
    {

        foreach (self::STEP_INTERESTS[$step] as $type => $interestedIn) {
            if (\Auth::user()->getInterestedType($type, $interestedIn) instanceof UserInterest && \Auth::user()->isInterestedInStep($type, $interestedIn)) {
                return true;
            }
        }
    }
}