<?php

namespace Tests\Unit\app\Rules;

use App\Rules\HouseNumber;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HouseNumberTest extends TestCase
{
	public static function houseNumberProvider(){
		return [
			[ 'nl', '1', true, ],
			[ 'nl', 'A', false, ],
			[ 'nl', '1A', true, ],
			[ 'nl', '1 A', true, ],
			[ 'nl', '1-A', true, ],
			[ 'nl', '1 - A', true, ],
			[ 'nl', 'A1', false, ],
			[ 'nl', '1-BOVEN', true, ],
		];
	}

	/**
	 * @dataProvider houseNumberProvider
	 */
	public function testPasses($country, $houseNumber, $shouldPass){
		$houseNumberRule = new HouseNumber($country);
		$this->assertEquals($shouldPass, $houseNumberRule->passes('number', $houseNumber));
	}
}
