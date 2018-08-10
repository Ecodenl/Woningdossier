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
                // interested in
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

    public static function isUserInterestedInMeasure($step)
    {

        foreach (self::STEP_INTERESTS[$step] as $type => $interestedIn) {
            if(\Auth::user()->getInterestedType($type, $interestedIn) instanceof UserInterest && \Auth::user()->isInterestedInStep($type, $interestedIn)) {
                return true;
            }
        }
    }
}