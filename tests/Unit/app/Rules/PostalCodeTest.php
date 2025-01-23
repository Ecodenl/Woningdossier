<?php

namespace Tests\Unit\app\Rules;

use PHPUnit\Framework\Attributes\DataProvider;
use App\Rules\PostalCode;
use Tests\TestCase;

final class PostalCodeTest extends TestCase
{
    public static function postalCodeProvider(): array
    {
        return [
            ['nl', '1000 AA', true],
            ['nl', '1000AA', true],
            ['nl', 'AA 1000', false],
            ['nl', '1000', false],
            ['nl', '1000 A', false],
            ['nl', '100 AA', false],
        ];
    }

    #[DataProvider('postalCodeProvider')]
    public function testPasses($country, $postalCode, $shouldPass): void
    {
        $postalCodeRule = new PostalCode($country);
        $this->assertEquals($shouldPass, $postalCodeRule->passes('postal_code', $postalCode));
    }
}
