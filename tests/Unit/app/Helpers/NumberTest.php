<?php

namespace Tests\Unit\app\Helpers;

use App\Helpers\Number;
use Tests\TestCase;

class NumberTest extends TestCase
{
    public static function isNegativeProvider()
    {
        return [
            [0.10, false],
            [19.10, false],
            [0.0, false],
            [0, false],
            [1.10, false],
            [0.1, false],
            // will be set to 0
            [-0, false],

            [-0.10, true],
            [-1, true],
            [-0.0, true],
            [-23, true],
        ];
    }

    /**
     * @dataProvider isNegativeProvider
     */
    public function testIsNegative($number, $expected)
    {
        $this->assertEquals($expected, Number::isNegative($number));
    }
}
