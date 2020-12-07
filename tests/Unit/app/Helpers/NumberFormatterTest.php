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
            ['nl', '1215.23.23', 0, '1215.23.23'],
            ['nl', '10.23.2', 1, '10.23.2'],
            ['nl', '123.456', 2, '123,46'],
            ['nl', '123456.789', 2, '123.456,79'],
            ['en', '123.456', 0, '123'],
            ['en', '1215.23.23', 0, '1215.23.23'],
            ['en', '10.23.2', 1, '10.23.2'],
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
            ['nl', '0', '0'],
            ['nl', '16.482.0', '16482.0'],
            ['en', '0', '0'],
            ['en', '16,482,00', '1648200'],
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

    public static function mathableFormatterProvider()
    {
        return [
            ['123.456', '2', '123.46'],
            ['123456.789', '3', '123456.789'],
            ['20.6', '0', '21'],
            ['0', '2', '0.00'],
            ['16.482.0', '3', '16.482.0'],
            ['0', '0', '0'],
            ['16482.00', '0', '16482'],
            ['123.456', '1', '123.5'],
            ['123.456.789', '3', '123.456.789'],
            ['49', '2', '49.00'],
            ['63.419', '2', '63.42'],
            ['300.5', '3', '300.500'],
            ['425986.123', '2', '425986.12'],
        ];
    }

    /**
     * @dataProvider mathableFormatterProvider
     */
    public function testMathableFormat($number, $decimals, $expected)
    {
        $this->assertEquals($expected, NumberFormatter::mathableFormat($number, $decimals));
    }


    public static function roundProvider()
    {
        return [
            [154, 5, 155.0],
            [1898.45, 5, 1900.0],
            [110, 5, 110.0],
            [2541512045, 5, 2541512045.0],
            ['10,5', 5, 10],
            ['108,5', 5, 110.0],
        ];
    }

    /**
     * @dataProvider roundProvider
     */
    public function testRound($number, $bucket, $expected)
    {
        $this->assertEquals($expected, NumberFormatter::round($number, $bucket));
    }
}
