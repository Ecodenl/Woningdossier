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
			[ 'nl', '-1', false, ],
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
