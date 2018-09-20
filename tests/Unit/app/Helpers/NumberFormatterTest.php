<?php

namespace Tests\Unit\app\Helpers;

use App\Helpers\NumberFormatter;
use Tests\TestCase;

class NumberFormatterTest extends TestCase
{
    public static function formatterProvider()
    {
        return [
            ['nl', '123.456', 0, '123'],
            ['nl', '123.456', 2, '123,46'],
            ['nl', '123456.789', 2, '123.456,79'],
            ['en', '123.456', 0, '123'],
            ['en', '123.456', 2, '123.46'],
            ['en', '123456.789', 2, '123,456.79'],
        ];
    }

    public static function reverseFormatterProvider()
    {
        return [
            ['nl', '123,456', '123.456'],
            ['nl', '123 456, 789', '123456.789'],
            ['nl', '20.6', '20.6'],
            ['en', '123.456', '123.456'],
            ['en', '123, 456. 789', '123456.789'],
        ];
    }

    /**
     * @dataProvider formatterProvider
     */
    public function testFormat($locale, $number, $decimals, $expected)
    {
        $this->app->setLocale($locale);

        $this->assertEquals($expected, NumberFormatter::format($number, $decimals));
    }

    /**
     * @dataProvider reverseFormatterProvider
     */
    public function testReverseFormat($locale, $number, $expected)
    {
        $this->app->setLocale($locale);

        $this->assertEquals($expected, NumberFormatter::reverseFormat($number));
    }
}
