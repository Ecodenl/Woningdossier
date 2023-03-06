<?php

namespace Tests\Unit\app\Helpers\DataTypes;

use App\Helpers\DataTypes\Caster;
use Tests\TestCase;

class CasterTest extends TestCase
{
    public function getCastProvider()
    {
        return [
            [Caster::STRING, 10, false, '10'],
            [Caster::STRING, '10', false, '10'],
            [Caster::STRING, 'gibberish', false, 'gibberish'],
            [Caster::STRING, null, false, ''],
            [Caster::STRING, true, false, '1'],
            [Caster::STRING, false, false, ''],
            [Caster::INT, 'gibberish', false, 0],
            [Caster::INT, '10', false, 10],
            [Caster::INT, '10.3', false, 10],
            [Caster::INT, '10.5', false, 11],
            [Caster::INT, '10.555', false, 11],
            [Caster::INT, '10,5', false, 10],
            [Caster::INT, '-10', false, -10],
            [Caster::INT, '-10.5', false, -11],
            [Caster::INT, '-0', false, 0],
            [Caster::INT, 13, false, 13],
            [Caster::INT, 13.7, false, 14],
            [Caster::INT, 13.7, false, 14],
            [Caster::INT, -13.2, false, -13],
            [Caster::INT, -13.7, false, -14],
            [Caster::INT, null, false, null],
            [Caster::INT, null, true, 0],
            [Caster::INT, true, false, 1],
            [Caster::INT, false, false, 0],
            [Caster::INT_5, 'gibberish', false, 0],
            [Caster::INT_5, '10', false, 10],
            [Caster::INT_5, '10.3', false, 10],
            [Caster::INT_5, '10.5', false, 10],
            [Caster::INT_5, '10.555', false, 10],
            [Caster::INT_5, '-10', false, -10],
            [Caster::INT_5, '-10.5', false, -10],
            [Caster::INT_5, '-12.5', false, -15],
            [Caster::INT_5, '-12,5', false, -10],
            [Caster::INT_5, '-0', false, 0],
            [Caster::INT_5, 13, false, 15],
            [Caster::INT_5, 13.7, false, 15],
            [Caster::INT_5, 12, false, 10],
            [Caster::INT_5, 12.7, false, 15],
            [Caster::INT_5, null, false, null],
            [Caster::INT_5, null, true, 0],
            [Caster::INT_5, true, false, 0],
            [Caster::INT_5, false, false, 0],
            [Caster::FLOAT, 'gibberish', false, 0.0],
            [Caster::FLOAT, '10', false, 10.0],
            [Caster::FLOAT, '10.3', false, 10.3],
            [Caster::FLOAT, '10.5', false, 10.5],
            [Caster::FLOAT, '10.555', false, 10.555],
            [Caster::FLOAT, '10.554', false, 10.554],
            [Caster::FLOAT, '10,5', false, 10.0],
            [Caster::FLOAT, '-10', false, -10.0],
            [Caster::FLOAT, '-10.5', false, -10.5],
            [Caster::FLOAT, '-12.5', false, -12.5],
            [Caster::FLOAT, '-12,5', false, -12.0],
            [Caster::FLOAT, '-0', false, 0.0],
            [Caster::FLOAT, 13, false, 13.0],
            [Caster::FLOAT, 13.7, false, 13.7],
            [Caster::FLOAT, 12, false, 12.0],
            [Caster::FLOAT, 12.7, false, 12.7],
            [Caster::FLOAT, null, false, null],
            [Caster::FLOAT, null, true, 0.0],
            [Caster::FLOAT, true, false, 1.0],
            [Caster::FLOAT, false, false, 0.0],
            [Caster::BOOL, 10, false, true],
            [Caster::BOOL, '10', false, true],
            [Caster::BOOL, 'gibberish', false, true],
            [Caster::BOOL, null, false, null],
            [Caster::BOOL, null, true, false],
            [Caster::BOOL, '', false, false],
            [Caster::BOOL, true, false, true],
            [Caster::BOOL, false, false, false],
            [Caster::ARRAY, 10, false, [10]],
            [Caster::ARRAY, '10', false, ['10']],
            [Caster::ARRAY, 'gibberish', false, ['gibberish']],
            [Caster::ARRAY, null, false, null],
            [Caster::ARRAY, null, true, []],
            [Caster::ARRAY, '', false, ['']],
            [Caster::ARRAY, true, false, [true]],
            [Caster::ARRAY, false, false, [false]],
            [Caster::JSON, '{"a":"a"}', false, ['a' => 'a']],
            [Caster::JSON, '{"a":"a","b":"b","c":{"a":"a"}}', false, ['a' => 'a', 'b' => 'b', 'c' => ['a' => 'a']]],
            [Caster::JSON, 10, false, 10],
            [Caster::JSON, 10.3, false, 10.3],
            [Caster::JSON, '10', false, '10'],
            [Caster::JSON, null, false, null],
            [Caster::JSON, null, true, null],
            [Caster::IDENTIFIER, 10, false, 10],
            [Caster::IDENTIFIER, 10.3, false, 10.3],
            [Caster::IDENTIFIER, null, false, null],
            [Caster::IDENTIFIER, null, true, null],
            [Caster::IDENTIFIER, [], false, []],
            [Caster::IDENTIFIER, 'aa', false, 'aa'],
        ];
    }

    /**
     * @dataProvider getCastProvider
     */
    public function test_get_cast(string $dataType, $value, bool $force, $expected)
    {
        $caster = Caster::init()->dataType($dataType)->value($value);
        if ($force) {
            $caster->force();
        }

        $this->assertEquals($expected, $caster->getCast());
    }

    public function reverseFormattedProvider()
    {
        // Provider note: The code only reverse formats ints and floats, as they are the only  user input that require
        // formatting. Therefore, this test is limited to these 2 only (in detail).
        return [
            // Test cases to prove the noted point.
            [Caster::STRING, 10, false, 10],
            [Caster::INT_5, 10, false, 10],
            [Caster::INT_5, null, false, null],
            [Caster::INT_5, null, true, null],
            [Caster::ARRAY, 10, false, 10],
            [Caster::ARRAY, null, false, null],
            [Caster::ARRAY, null, true, null],
            [Caster::JSON, 10, false, 10],
            [Caster::JSON, null, true, null],
            // Actual useful tests. Use case: user fills in data, we get something out of it.
            [Caster::INT, null, false, null],
            [Caster::INT, null, true, 0],
            [Caster::INT, '10', false, 10],
            [Caster::INT, '10.3', false, 103], // Dutch format expected, so "wrong" input gets parsed.
            [Caster::INT, '10,3', false, 10],
            [Caster::INT, '10.73', false, 1073],
            [Caster::INT, '10,73', false, 11],
            [Caster::INT, '10,7333', false, 11],
            [Caster::INT, 'AmIANumber?', false, 0],
            [Caster::FLOAT, null, false, null],
            [Caster::FLOAT, null, true, 0.0],
            [Caster::FLOAT, '10', false, 10.0],
            [Caster::FLOAT, '10.3', false, 103.0],
            [Caster::FLOAT, '10,3', false, 10.3],
            [Caster::FLOAT, '10.73', false, 1073.0],
            [Caster::FLOAT, '10,73', false, 10.73],
            [Caster::FLOAT, '10,7333', false, 10.73],
            [Caster::FLOAT, '10,735', false, 10.74],
            [Caster::FLOAT, 'AmIANumber?', false, 0.0],
        ];
    }

    /**
     * @dataProvider reverseFormattedProvider
     */
    public function test_reverse_formatted(string $dataType, $value, bool $force, $expected)
    {
        $caster = Caster::init()->dataType($dataType)->value($value);
        if ($force) {
            $caster->force();
        }

        $this->assertEquals($expected, $caster->reverseFormatted());
    }

    public function getFormatForUserProvider()
    {
        // Provider note: The code only formats ints (and bucketed ints) and floats, as they are the only that
        // (currently) require formatting. The other values apply a `getCast()`. See other tests on those results.
        return [
            // Use case: backend returns data, it's formatted for the user.
            [Caster::INT, null, false, null],
            [Caster::INT, null, true, '0'],
            [Caster::INT, '0', false, '0'],
            [Caster::INT, '10', false, '10'],
            [Caster::INT, '10.3', false, '10'],
            [Caster::INT, '10,3', false, '10'],
            [Caster::INT, '10.73', false, '11'],
            [Caster::INT, '10,73', false, '10'],
            [Caster::INT, 'AmIANumber?', false, null],
            [Caster::INT_5, null, false, null],
            [Caster::INT_5, null, true, '0'],
            [Caster::INT_5, '0', false, '0'],
            [Caster::INT_5, '10', false, '10'],
            [Caster::INT_5, '10.3', false, '10'],
            [Caster::INT_5, '10,3', false, '10'],
            [Caster::INT_5, '10.73', false, '10'],
            [Caster::INT_5, '12.73', false, '15'],
            [Caster::INT_5, '13', false, '15'],
            [Caster::INT_5, '10,73', false, '10'],
            [Caster::INT_5, 'AmIANumber?', false, null],
            [Caster::FLOAT, null, false, null],
            [Caster::FLOAT, null, true, '0'],
            [Caster::FLOAT, '0', false, '0'],
            [Caster::FLOAT, '10', false, '10,0'],
            [Caster::FLOAT, '10.3', false, '10,3'],
            [Caster::FLOAT, '10,3', false, '10,0'],
            [Caster::FLOAT, '10.73', false, '10,7'],
            [Caster::FLOAT, '10,73', false, '10,0'],
            [Caster::FLOAT, '10,7333', false, '10,0'],
            [Caster::FLOAT, '10,735', false, '10,0'],
            [Caster::FLOAT, '10,75', false, '10,0'],
            [Caster::FLOAT, '10.75', false, '10,8'],
            [Caster::FLOAT, 'AmIANumber?', false, null],
        ];
    }

    /**
     * @dataProvider getFormatForUserProvider
     */
    public function test_get_format_for_user(string $dataType, $value, bool $force, $expected)
    {
        $caster = Caster::init()->dataType($dataType)->value($value);
        if ($force) {
            $caster->force();
        }

        // Note: Test currently does not support locale. When we do add a second locale, this will need revisiting.
        $this->assertEquals($expected, $caster->getFormatForUser());
    }
}