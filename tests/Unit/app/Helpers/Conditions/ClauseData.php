<?php

namespace Tests\Unit\app\Helpers\Conditions;

class ClauseData
{

    public static function arraySimpleSingleClause()
    {
        return [
            [
                [
                    'column'   => 'A',
                    'operator' => '=',
                    'value'    => 1,
                ],
            ],
        ];
    }

    public static function arraySimpleAndClause()
    {
        return [
            [
                [
                    'column'   => 'A',
                    'operator' => '=',
                    'value'    => 1,
                ],
                [
                    'column'   => 'B',
                    'operator' => '>',
                    'value'    => 1,
                ],
            ],
        ];
    }

    public static function arrayAndOrClause()
    {
        return [
            [
                [
                    'column'   => 'A',
                    'operator' => '=',
                    'value'    => 1,
                ],
                [
                    'column'   => 'B',
                    'operator' => '>',
                    'value'    => 1,
                ],
            ],
            [
                [
                    'column'   => 'C',
                    'operator' => '=',
                    'value'    => 100,
                ],
            ],
        ];
    }
}