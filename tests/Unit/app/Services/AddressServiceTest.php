<?php

namespace Tests\Unit\app\Services;

use App\Services\AddressService;
use Tests\TestCase;

class AddressServiceTest extends TestCase
{
    public function normalizeZipcodeProvider()
    {
        return [
            ['1000AA', false, '1000AA'],
            ['1000aa', false, '1000AA'],
            ['1000AA', true, '1000 AA'],
            ['1000aa', true, '1000 AA'],
            ['1000 AA', false, '1000AA'],
            ['1000 aa', false, '1000AA'],
            ['1000 AA', true, '1000 AA'],
            ['1000 aa', true, '1000 AA'],
            ['1000A', false, '1000A'],
            ['1000a', false, '1000A'],
            ['1000 a', false, '1000A'],
            ['1000 ', false, '1000'],
            ['100a', false, '100A'],
            ['100a ', false, '100A'],
            ['1000A', true, '1000 A'],
            ['1000a', true, '1000 A'],
            ['1000 a', true, '1000 A'],
            ['1000 ', true, '1000'],
            ['100a', true, '100A'],
            ['100a ', true, '100A'],
            ['', false, ''],
            ['', true, ''],
            ['0', false, '0'],
            ['0', true, '0'],
            [0, false, 0],
            [0, true, 0],
            [null, false, null],
            [null, true, null],
        ];
    }

    /**
     * @dataProvider normalizeZipcodeProvider
     */
    public function test_normalize_zipcode($zipcode, $withSpace, $expected): void
    {
        $normalized = (new AddressService())->normalizeZipcode($zipcode, $withSpace);
        $this->assertEquals($expected, $normalized);
    }
}