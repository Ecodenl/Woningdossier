<?php

namespace Tests\Unit\app\Helpers;

use App\Helpers\Calculator;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CalculatorTest extends TestCase
{

	public static function indexCostsProvider(){
		return [
			[ 100, null, 2023, 110.40808032 ],
			[ 100, null, 2021, 106.12080000000002 ],
			[ 106.12080000000002, 2021, 2023, 110.40808032 ],
			[ 110.40808032, 2023, 2018, 100 ],
		];
	}

	/**
	 * @dataProvider indexCostsProvider
	 */
	public function testReindexCosts($costs, $from, $to, $expected){
		$this->assertEquals($expected, Calculator::reindexCosts($costs, $from, $to));
	}
}
