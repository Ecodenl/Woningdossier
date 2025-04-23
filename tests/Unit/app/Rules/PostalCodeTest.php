<?php

namespace Tests\Unit\app\Rules;

use App\Rules\PostalCode;
use Tests\TestCase;

class PostalCodeTest extends TestCase
{
    public static function postalCodeProvider()
    {
        return [
            ['nl', '1000 AA', true],
            ['nl', '1000AA', true],
            ['nl', 'AA 1000', false],
            ['nl', '1000', false],
            ['nl', '1000 A', false],
            ['nl', '100 AA', false],
            ['be', '1000 AA', false],
            ['be', '1000AA', false],
            ['be', 'AA 1000', false],
            ['be', '1000', true],
            ['be', '9000', true],
            ['be', '0000', false],
            ['be', '1000 A', false],
            ['be', '100 AA', false],
        ];
    }

    /**
     * @dataProvider postalCodeProvider
     */
    public function testPasses($country, $postalCode, $shouldPass)
    {
        $postalCodeRule = new PostalCode($country);
        $this->assertEquals($shouldPass, $postalCodeRule->passes('postal_code', $postalCode));
    }
}
