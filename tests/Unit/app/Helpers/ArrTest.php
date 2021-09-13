<?php

namespace Tests\Unit\app\Helpers;

use App\Helpers\Arr;
use Tests\TestCase;

class ArrTest extends TestCase
{
    public static function dottedArrayProvider()
    {
        return [
            [['products.desk.price' => 100], ['products' => ['desk' => ['price' => 100]]]],
        ];
    }

    /**
     * @dataProvider dottedArrayProvider
     */
    public function testArrayUndot($input, $expected)
    {
        $this->assertEquals($expected, Arr::arrayUndot($input));
    }

    public static function isWholeArrayEmptyProvider()
    {
        return [
            [
                [
                    'Bewoner' => [
                        'element' => '',
                        'service' => '',
                    ],
                    'Coach' => [
                        'element' => 'een comment dat dus absoluut niet leeg is.',
                        'service' => '',
                    ],
                ],
                false,
            ],
            [
                [
                    'Bewoner' => [
                        'element' => null,
                    ],
                    'Coach' => [
                        'service' => '',
                    ],
                ],
                true,
            ],
            [
                [
                    'Bewoner' => [
                        'element' => 'null',
                    ],
                    'Coach' => [
                        'service' => '0.00',
                    ],
                ],
                true,
            ],
            [
                [
                    'key' => [],
                    'key2' => ['value'],
                ],
                false,
            ],
            [
                [
                    'key' => [],
                    'key2' => ['subKey' => []],
                ],
                true,
            ],
            [
                [
                    'key' => [],
                ],
                true,
            ],
        ];
    }

    /**
     * @dataProvider isWholeArrayEmptyProvider
     */
    public function testIsWholeArrayEmpty($input, $expected)
    {
        $this->assertEquals($expected, Arr::isWholeArrayEmpty($input));
    }
}
