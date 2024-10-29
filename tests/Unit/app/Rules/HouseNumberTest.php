<?php

namespace Tests\Unit\app\Rules;

use PHPUnit\Framework\Attributes\DataProvider;
use App\Rules\HouseNumber;
use Tests\TestCase;

final class HouseNumberTest extends TestCase
{
    public static function houseNumberProvider(): array
    {
        return [
            ['nl', '1', true],
            ['nl', '-1', false],
        ];
    }

    #[DataProvider('houseNumberProvider')]
    public function testPasses($country, $houseNumber, $shouldPass): void
    {
        $houseNumberRule = new HouseNumber($country);
        $this->assertEquals($shouldPass, $houseNumberRule->passes('number', $houseNumber));
    }
}
