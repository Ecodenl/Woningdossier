<?php

namespace Tests\Unit\app\Rules;

use App\Rules\HouseNumber;
use Tests\TestCase;

class HouseNumberTest extends TestCase
{
    public static function houseNumberProvider(): array
    {
        return [
            ['nl', '1', true],
            ['nl', '-1', false],
        ];
    }

    /**
     * @dataProvider houseNumberProvider
     */
    public function testPasses($country, $houseNumber, $shouldPass): void
    {
        $houseNumberRule = new HouseNumber($country);
        $this->assertEquals($shouldPass, $houseNumberRule->passes('number', $houseNumber));
    }
}
