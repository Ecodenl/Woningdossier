<?php

namespace Tests\Unit\app\Helpers\Calculation;

use PHPUnit\Framework\Attributes\DataProvider;
use App\Helpers\Calculation\BankInterestCalculator;
use Tests\TestCase;

final class BankInterestCalculatorTest extends TestCase
{
    public static function dataProvider(): array
    {
        return [
            [
                2690,
                84.00,
                2.00,
                25,
            ],
            [
                3042,
                95.00,
                2.00,
                25,
            ],
        ];
    }

    #[DataProvider('dataProvider')]
    public function testTw($expected, $amount, $interest, $period): void
    {
        $this->assertEquals($expected, BankInterestCalculator::tw($amount, $interest, $period));
    }
}
