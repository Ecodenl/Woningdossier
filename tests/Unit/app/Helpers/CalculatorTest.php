<?php

namespace Tests\Unit\app\Helpers;

use PHPUnit\Framework\Attributes\DataProvider;
use App\Helpers\RawCalculator;
use Tests\TestCase;

class CalculatorTest extends TestCase
{
    public static function indexCostsProvider(): array
    {
        $year = (int) date('Y');

        return [
            [100, null, ($year + 5), 110.40808032, 2],
            [100, null, ($year + 3), 106.12080000000002, 2],
            [106.12080000000002, 2021, 2023, 110.40808032, 2],
            [110.40808032, 2023, 2018, 100, 2],
        ];
    }

    #[DataProvider('indexCostsProvider')]
    public function testReindexCosts($costs, $from, $to, $expected, $percentage): void
    {
        $this->markTestSkipped('must be revisited.');
        $this->assertEquals($expected, RawCalculator::reindexCosts($costs, $from, $to, $percentage));
    }
}
