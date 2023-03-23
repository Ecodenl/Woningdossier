<?php

namespace Tests\Unit\app\Rules;

use App\Rules\HouseNumberExtension;
use Tests\TestCase;

class HouseNumberExtensionTest extends TestCase
{
    public static function houseNumberExtensionProvider()
    {
        return [
            // Passes
            ['nl', 'a', true],
            ['nl', 'bis', true],
            ['nl', '11', true],
            ['nl', 'boven', true],
            ['nl', 'a1', true],
            ['nl', '1c', true],
            ['nl', 'b1ba', true],
            ['nl', 'beneden', true],
            ['nl', 'bis A', true],
            ['nl', 'BIS A', true],
            ['nl', '1/2', true],
            ['nl', '99999', true],
            ['nl', '12a', true],
            ['nl', 'ZW', true],
            ['nl', 'zwart', true],
            ['nl', 'rood', true],
            ['nl', 'RD', true],
            ['nl', 'RD', true],
            // Fails
            ['nl', '12_3', false],
            ['nl', '999999', false],
            ['nl', '-_/', false],
            ['nl', '-_-', false],
            ['nl', 'ObviouslyFalse', false],
            ['nl', 'foutMan', false],
            ['nl', 'NietGeldig', false],
            ['nl', '-123', false],
        ];
    }

    /**
     * @dataProvider houseNumberExtensionProvider
     */
    public function testPasses($country, $houseNumberExtension, $shouldPass)
    {
        $houseNumberExtensionRule = new HouseNumberExtension($country);
        $this->assertEquals($shouldPass, $houseNumberExtensionRule->passes('house_number_extension', $houseNumberExtension));
    }
}
