<?php

namespace Tests\Unit\app\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use App\Helpers\NumberFormatter;
use Tests\TestCase;

class NumberFormatterTest extends TestCase
{
    public static function formatterProvider(): array
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

    #[DataProvider('formatterProvider')]
    public function testFormat($locale, $number, $decimals, $expected): void
    {
        $this->app->setLocale($locale);

        $this->assertEquals($expected, NumberFormatter::format($number, $decimals));
    }

    public static function reverseFormatterProvider(): array
    {
        return [
            ['nl', '123,456', '123.456'],
            ['nl', '123 456, 789', '123456.789'],
            ['nl', '20.6', '20.6'],
            ['nl', '0', '0'],
            ['nl', '16.482.0', '16482.0'],
            ['nl', '', '0'],
            ['nl', null, '0'],
            ['nl', 'test', '0'],
            ['en', '0', '0'],
            ['en', '16,482,00', '1648200'],
            ['en', '123.456', '123.456'],
            ['en', '123, 456. 789', '123456.789'],
            ['en', '', '0'],
            ['en', null, '0'],
            ['en', 'test', '0'],
        ];
    }

    #[DataProvider('reverseFormatterProvider')]
    public function testReverseFormat($locale, $number, $expected): void
    {
        $this->app->setLocale($locale);

        $this->assertEquals($expected, NumberFormatter::reverseFormat($number));
    }

    public static function mathableFormatterProvider(): array
    {
        return [
            ['125.400', 2, 125.40],
            ['12500,45', 2, 12500.45],
            ['13,45', 2, 13.45],
            ['11,69', 1, 11.7],
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

    #[DataProvider('mathableFormatterProvider')]
    public function testMathableFormat($number, $decimals, $expected): void
    {
        $this->assertEquals($expected, NumberFormatter::mathableFormat($number, $decimals));
    }

    public static function roundProvider(): array
    {
        return [
            [154, 5, 155.0],
            [1898.45, 5, 1900.0],
            [110, 5, 110.0],
            [2541512045, 5, 2541512045.0],
            ['10,5', 5, 10],
            ['108,5', 5, 110.0],
            ['-0,92', 5, 0.0],
            ['-0.92', 5, 0.0],
            ['-0.92', 0, -1.0],
            ['-0,92', 1, -1.0],
            [-0.92, 5, 0.0],
            [-1.2, 5, 0.0],
            [-1.2, 0, -1],
            [-1.2, 1, -1],
            [-1.5, 1, -2],
            ['', 5, 0],
            [null, 5, 0],
            ['test', 5, 0],
        ];
    }

    #[DataProvider('roundProvider')]
    public function testRound($number, $bucket, $expected): void
    {
        $this->assertEquals($expected, NumberFormatter::round($number, $bucket));
    }

    public static function rangeProvider(): array
    {
        // Expected is in locale en
        return [
            [50, 100, 0, '-', '', '50-100'],
            [50, 100, 2, '-', '', '50.00-100.00'],
            [50, 100, 0, ' - ', '€', '€50 - €100'],
            [null, 100, 0, '-', '', '100'],
            [50, null, 0, '-', '', '50'],
            [0, 100, 0, '-', '', '0-100'],
            [0, 100, 1, '-', '', '0.0-100.0'],
            [50, null, 0, '-', '£ ', '£ 50'],
            [null, null, 0, '-', '£ ', '0'],
        ];
    }

    #[DataProvider('rangeProvider')]
    public function testRange($from, $to, $decimals, $separator, $prefix, $expected): void
    {
        app()->setLocale('en');

        $this->assertEquals($expected, NumberFormatter::range($from, $to, $decimals, $separator, $prefix));
    }

    public static function prefixProvider(): array
    {
        return [
            [50, '€', '€50'],
            [null, '€', '€'],
            [null, '', ''],
            [null, null, ''],
            ['203', '--', '--203'],
        ];
    }

    #[DataProvider('prefixProvider')]
    public function testPrefix($value, $prefix, $expected): void
    {
        $this->assertEquals($expected, NumberFormatter::prefix($value, $prefix));
    }

    public static function formatNumberForUserProvider(): array
    {
        return [
            ['0,00', true, true, '0'],
            ['0,00', false, true, '0'],
            ['0.00', true, true, '0'],
            ['2.000.000', true, true, '2000000'],
            ['2001', true, true, '2001'],
            ['2001', false, true, '2.001,0'],
            ['2.001', false, true, '2,0'],
            ['500', false, true, '500,0'],
            ['0', false, false, '0.0'],
            ['0.00', false, false, '0.0'],
            ['10.3', false, false, '10,3'],
            ['10.3', true, false, 10],
            [null, true, false, null],
            ['', true, false, null],
        ];
    }

    #[DataProvider('formatNumberForUserProvider')]
    public function testFormatNumberForUser($number, $isInteger, $alwaysNumber, $expected): void
    {
        // Note: Test currently does not support locale. When we do add a second locale, this will need revisiting.
        $this->assertEquals($expected, NumberFormatter::formatNumberForUser($number, $isInteger, $alwaysNumber));
    }
}
