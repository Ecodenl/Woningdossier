<?php

namespace Tests\Unit\app\Rules;

use PHPUnit\Framework\Attributes\DataProvider;
use App\Rules\HashCheck;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class HashCheckTest extends TestCase
{
    public static function HashCheckProvider(): array
    {
        return [
            ['secret', 'secret', true],
            ['asdfohasuidfhu', 'oisdfhuisdfh', false],
            ['wedesignit', 'Wedesignit', false],
            ['GitHub12!', 'GitHub12!', true],
            ['Super!Secret!Password80', 'Super!Secret!Password80', true],
        ];
    }

    #[DataProvider('HashCheckProvider')]
    public function testPasses($valueToCheck, $unhashedValue, $shouldPass): void
    {
        $postalCodeRule = new HashCheck(Hash::make($valueToCheck));
        $this->assertEquals($shouldPass, $postalCodeRule->passes('password', $unhashedValue));
    }
}
