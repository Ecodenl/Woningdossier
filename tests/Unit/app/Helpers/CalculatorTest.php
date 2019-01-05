<?php

namespace Tests\Unit\app\Helpers;

use App\Helpers\Calculator;
use Tests\TestCase;

class CalculatorTest extends TestCase
{
    public static function indexCostsProvider()
    {
	    $year = (int) date('Y');
        return [
            [100, null, ($year + 5), 110.40808032, 2],
            [100, null, ($year + 3), 106.12080000000002, 2],
            [106.12080000000002, 2021, 2023, 110.40808032, 2],
            [110.40808032, 2023, 2018, 100, 2],
        ];
    }

    /**
     * @dataProvider indexCostsProvider
     */
    public function testReindexCosts($costs, $from, $to, $expected, $percentage)
    {
        $this->assertEquals($expected, Calculator::reindexCosts($costs, $from, $to, $percentage));
    }
}
