<?php

namespace Tests\Unit\app\Rules;

use App\Rules\HouseNumberExtension;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HouseNumberExtensionTest extends TestCase
{

	public static function houseNumberExtensionProvider(){
		return [
			[ 'nl', 'a', true, ],
			[ 'nl', 'bis', true, ],
			[ 'nl', '11', false, ],
			[ 'nl', 'boven', true, ],
		];
	}

	/**
	 * @dataProvider houseNumberExtensionProvider
	 */
	public function testPasses($country, $houseNumberExtension, $shouldPass){
		$houseNumberExtensionRule = new HouseNumberExtension($country);
		$this->assertEquals($shouldPass, $houseNumberExtensionRule->passes('house_number_extension', $houseNumberExtension));
	}
}
