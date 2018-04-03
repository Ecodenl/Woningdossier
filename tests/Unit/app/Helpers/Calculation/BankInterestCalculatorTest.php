<?php

namespace Tests\Unit\app\Helpers\Calculation;

use App\Helpers\Calculation\BankInterestCalculator;
use Tests\TestCase;

class BankInterestCalculatorTest extends TestCase
{

	public static function dataProvider(){
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

	/**
	 * @dataProvider dataProvider
	 */
	public function testTw($expected, $amount, $interest, $period){
		$this->assertEquals($expected, BankInterestCalculator::tw($amount, $interest, $period));
	}
}
