<?php

namespace Tests\Unit\app\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use App\Helpers\NumberFormatter;
use Tests\TestCase;

final class NumberFormatterTest extends TestCase
{
    public static function formatterProvider(): array
    {
        return [
            ['nl', '123.456', 0, '123'],
            ['nl', '1215.23.23', 0, '1215.23.23'],
            ['nl', '10.23.2', 1, '10.23.2'],
            ['nl', '123.456', 2, '123,46'],
            ['nl', '123456.789', 2, '123.456,79'],
            ['nl', 'gibberish', 2, 'gibberish'],
            ['nl', null, 2, '0,00'],
            ['en', '123.456', 0, '123'],
            ['en', '1215.23.23', 0, '1215.23.23'],
            ['en', '10.23.2', 1, '10.23.2'],
            ['en', '123.456', 2, '123.46'],
            ['en', '123456.789', 2, '123,456.79'],
            ['en', 'gibberish', 2, 'gibberish'],
            ['en', null, 2, '0.00'],
        ];
    }

    #[DataProvider('formatterProvider')]
    public function testFormat(string $locale, null|string|int|float $number, int $decimals, ?string $expected): void
    {
        $this->app->setLocale($locale);

        $this->assertSame($expected, NumberFormatter::format($number, $decimals));
    }

    public static function reverseFormatterProvider(): array
    {
        return [
            ['nl', '123,456', 123.456],
            ['nl', '123.456', 123.456],
            ['nl', '123.456.789', 123456.789],
            ['nl', '123 456, 789', 123456.789],
            ['nl', '123, 456. 789', 0.0],
            ['nl', '123.456,789', 0.0],
            ['nl', '123,456.789', 0.0],
            ['nl', '20.6', 20.6],
            ['nl', '20,6', 20.6],
            ['nl', '16.482.0', 16482.0],
            ['nl', '0', 0.00],
            ['nl', '', 0.00],
            ['nl', null, 0.00],
            ['nl', 'test', 0.00],
            ['en', '123,456', 123456.0],
            ['en', '123.456', 123.456],
            ['en', '123.456.789', 123456.789],
            ['en', '123 456, 789', 123456789.0],
            ['en', '123, 456. 789', 123456.789],
            ['en', '123.456,789', 123.456789],
            ['en', '123,456.789', 123456.789],
            ['en', '20.6', 20.6],
            ['en', '20,6', 206.0],
            ['en', '16,482,0', 164820.0],
            ['en', '0', 0.00],
            ['en', '', 0.00],
            ['en', null, 0.00],
            ['en', 'test', 0.00],
        ];
    }

    #[DataProvider('reverseFormatterProvider')]
    public function testReverseFormat($locale, $number, $expected): void
    {
        $this->app->setLocale($locale);

        $this->assertSame($expected, NumberFormatter::reverseFormat($number));
    }

    public static function mathableFormatterProvider(): array
    {
        return [
            ['125.400', 2, '125.40'],
            [125.400, 2, '125.40'],
            ['12500,45', 2, '12500.45'],
            ['13,45', 2, '13.45'],
            ['11,69', 1, '11.7'],
            ['123.456', '2', '123.46'],
            ['123456.789', '3', '123456.789'],
            ['20.6', '0', '21'],
            ['0', '2', '0.00'],
            [0, '2', '0.00'],
            ['16.482.0', '3', '16.482.0'],
            ['0', '0', '0'],
            ['16482.00', '0', '16482'],
            ['123.456', '1', '123.5'],
            ['123.456.789', '3', '123.456.789'],
            ['49', '2', '49.00'],
            [49, '2', '49.00'],
            ['63.419', '2', '63.42'],
            ['300.5', '3', '300.500'],
            ['425986.123', '2', '425986.12'],
        ];
    }

    #[DataProvider('mathableFormatterProvider')]
    public function testMathableFormat($number, $decimals, $expected): void
    {
        $this->assertSame($expected, NumberFormatter::mathableFormat($number, $decimals));
    }

    public static function roundProvider(): array
    {
        return [
            [154, 5, 155],
            [1898.45, 5, 1900],
            [110, 5, 110],
            [2541512045, 5, 2541512045],
            ['10,5', 5, 10],
            ['108,5', 1, 109],
            ['108,5', 5, 110],
            ['108,5', 25, 100],
            ['115,5', 25, 125],
            ['19,5', 25, 25],
            ['-0,92', 5, 0],
            ['-0.92', 5, 0],
            ['-0.92', 0, -1],
            ['-0,92', 1, -1],
            [-0.92, 5, 0],
            [-1.2, 5, 0],
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
        $this->assertSame($expected, NumberFormatter::round($number, $bucket));
    }

    public static function rangeProvider(): array
    {
        // Expected is in locale en
        return [
            [50, 100, 0, '-', '', '50-100'],
            [50, 100, 2, '-', '', '50.00-100.00'],
            [50.20, 100.20, 0, '-', '', '50-100'],
            [50.50, 100.50, 0, '-', '', '51-101'],
            [50.2, 100.2, 2, '-', '', '50.20-100.20'],
            [50.20, 100.20, 2, '-', '', '50.20-100.20'],
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
    public function testRange(null|string|int|float $from, null|string|int|float $to, int $decimals, string $separator, string $prefix, string $expected): void
    {
        app()->setLocale('en');

        $this->assertSame($expected, NumberFormatter::range($from, $to, $decimals, $separator, $prefix));
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
        $this->assertSame($expected, NumberFormatter::prefix($value, $prefix));
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
            ['0', false, false, '0'],
            ['0.00', false, false, '0'],
            ['10.3', false, false, '10,3'],
            ['10.3', true, false, '10'],
            [null, true, false, null],
            ['', true, false, null],
        ];
    }

    #[DataProvider('formatNumberForUserProvider')]
    public function testFormatNumberForUser($number, bool $isInteger, bool $alwaysNumber, $expected): void
    {
        // Note: Test currently does not support locale. When we do add a second locale, this will need revisiting.
        $this->assertSame($expected, NumberFormatter::formatNumberForUser($number, $isInteger, $alwaysNumber));
    }
}
