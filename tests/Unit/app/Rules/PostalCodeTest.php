<?php

namespace Tests\Unit\app\Rules;

use App\Rules\PostalCode;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostalCodeTest extends TestCase
{
	public static function postalCodeProvider(){
		return [
			[ 'nl', '1000 AA', true, ],
			[ 'nl', '1000AA', true, ],
			[ 'nl', 'AA 1000', false, ],
			[ 'nl', '1000', false, ],
			[ 'nl', '1000 A', false, ],
			[ 'nl', '100 AA', false, ],
		];
	}

	/**
	 * @dataProvider postalCodeProvider
	 */
	public function testPasses($country, $postalCode, $shouldPass){
		$postalCodeRule = new PostalCode($country);
		$this->assertEquals($shouldPass, $postalCodeRule->passes('postal_code', $postalCode));
	}
}
