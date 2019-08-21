<?php

namespace Tests\Unit\app\Rules;

use App\Rules\HashCheck;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HashCheckTest extends TestCase
{
    public static function HashCheckProvider()
    {
        return [
            ['secret', 'secret', true],
            ['asdfohasuidfhu', 'oisdfhuisdfh', false],
            ['wedesignit', 'Wedesignit', false],
            ['GitHub12!', 'GitHub12!', true],
            ['Super!Secret!Password80', 'Super!Secret!Password80', true],
        ];
    }

    /**
     * @dataProvider HashCheckProvider
     */
    public function testPasses($valueToCheck, $unhashedValue, $shouldPass)
    {
        $postalCodeRule = new HashCheck(Hash::make($valueToCheck));
        $this->assertEquals($shouldPass, $postalCodeRule->passes('password', $unhashedValue));
    }
}
