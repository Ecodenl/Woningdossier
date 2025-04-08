<?php

namespace Tests\Unit\app\Rules;

use App\Rules\PhoneNumber;
use Tests\TestCase;

class PhoneNumberTest extends TestCase
{
    public static function phoneNumberProvider()
    {
        return [
            ['nl', '+31612345678', true],
            ['nl', '+31(0)612345678', true],
            ['nl', '(+31)(0)612345678', true],
            ['nl', '(034) 123-1234', true],
            ['nl', '(+31)0612345678', false],
            ['nl', '0612345678', true],
            ['nl', '06-12345678', true],
            ['nl', '+316-12345678', true],
            ['nl', '0031612345678', true],
            ['nl', '00310612345678', true],
            ['nl', '(0031)0612345678', false],
            ['nl', '(0031)(0)612345678', false],
            ['nl', 'fdkslfasjl', false],
            ['nl', '59204585920', false],
            ['nl', '1612345678', false],
            ['nl', '(058) 1234567', true],
            ['nl', '058 1234567', true],
            ['nl', '058 123 45 67', true],
            ['nl', '+32612345678', true], // TODO: Currently valid, should it be?
            ['nl', '0032612345678', true], // TODO: Currently valid, should it be?
            // Belgian regex supports (only) mobile numbers with or without country code, but not invalid brackets or dashes
            ['be', '0474123456', true],
            ['be', '+32474123456', true],
            ['be', '0032474123456 ', true],
            ['be', '+32(0)474123456 ', false],
            ['be', '(+32)(0)474123456 ', false],
            ['be', '(0032)(0)474123456 ', false],
            ['be', '0455123456', true],
            ['be', '+32455123456', true],
            ['be', '0032455123456', true],
            ['be', '0456123456', true],
            ['be', '0457123456', false], // Incorrect mobile owner range
            ['be', '0460123456', true],
            ['be', '0461123456', false], // Incorrect mobile owner range
            ['be', '0465123456', true],
            ['be', '0466123456', true],
            ['be', '0467123456', true],
            ['be', '0468123456', true],
            ['be', '0469123456', false], // Incorrect mobile owner range
            ['be', '0471123456', true],
            ['be', '0472123456', true],
            ['be', '+32472123456', true],
            ['be', '0473123456', true],
            ['be', '+320473123456', false],
            ['be', '0488123456', true],
            ['be', '+488123456', false],
            ['be', '+0488123456', false],
            ['be', '000488123456', false],
            ['be', '0032488123456', true],
            ['be', '0487123456', true],
            ['be', '0493123456', true],
            ['be', '0490123456', true],
            ['be', '031234567', false], // No support for non-mobile numbers (for now?)
            ['be', '+3231234567', false], // No support for non-mobile numbers (for now?)
            // Non-BE numbers / Gibberish
            ['be', '+31612345678', false],
            ['be', '+31(0)612345678', false],
            ['be', '(+31)(0)612345678', false],
            ['be', '(034) 123-1234', false],
            ['be', '(+31)0612345678', false],
            ['be', '0612345678', false],
            ['be', '06-12345678', false],
            ['be', '+316-12345678', false],
            ['be', '0031612345678', false],
            ['be', '00310612345678', false],
            ['be', '(0031)0612345678', false],
            ['be', '(0031)(0)612345678', false],
            ['be', 'fdkslfasjl', false],
            ['be', '59204585920', false],
            ['be', '1612345678', false],

        ];
    }

    /**
     * @dataProvider phoneNumberProvider
     */
    public function testPasses($country, $phoneNumber, $shouldPass)
    {
        $phoneNumberRule = new PhoneNumber($country);
        $this->assertEquals($shouldPass, $phoneNumberRule->passes('phone_number', $phoneNumber));
    }
}
