<?php

namespace Tests\Unit\app\Helpers;

use App\Helpers\Str;
use Tests\TestCase;

class StrTest extends TestCase
{
    public static function isConsideredEmptyAnswerProvider()
    {
        return [
            [null, true],
            ['null', true],
            ['0', true],
            [0, true],
            ['0.00', true],
            ['0.0', true],
            ['0,0', true],
            ['0,00', true],
            ['test', false],
            [' ', false],
            [23, false],
        ];
    }

    /**
     * @dataProvider isConsideredEmptyAnswerProvider
     */
    public function testIsConsideredEmptyAnswer($values, $expected)
    {
        $this->assertEquals($expected, Str::isConsideredEmptyAnswer($values));
    }

    public static function lcfirstProvider()
    {
        return [
            [0, '0'],
            [null, ''],
            ['TesT', 'tesT'],
            ['test', 'test'],
            ['ALLCAPS', 'aLLCAPS'],
        ];
    }

    /**
     * @dataProvider lcfirstProvider
     */
    public function testLcfirst($value, $expected)
    {
        $this->assertEquals($expected, Str::lcfirst($value));
    }

    public static function isValidJsonProvider()
    {
        return [
            ['{"has_crawlspace":"yes","access":"yes"}', true, true],
            ['{"has_crawlspace":"yes","access":"yes"}', false, true],
            [12, false, true],
            [12, true, false],
            [null, false, false],
            ['null', true, false],
            ['NotJson', true, false],
            ['{"can_edit":"true"}', false, true],
            ['"thisIsJsonButWeWantFalse"', true, false],
            ['"thisIsJsonAndWeWantTrue"', false, true],
        ];
    }

    /**
     * @dataProvider isValidJsonProvider
     */
    public function testIsValidJson($value, $arrayOnly, $expected)
    {
        $this->assertEquals($expected, Str::isValidJson($value, $arrayOnly));
    }

    public static function arrContainsProvider()
    {
        return [
            [[], null, false, false],
            [[], 'null', false, false],
            [[], 'null', true, false],
            [[], null, true, false],
            [['test'], null, false, false],
            [['test'], null, true, false],
            [['test'], 'null', false, false],
            [['test'], 'null', true, false],
            [['test'], 'test', false, true],
            [['test', 'hoomdossier'], 'hoom', false, true],
            [['test', 'hoomdossier'], 'Hoom', false, false],
            [['test', 'hoomdossier'], 'Hoom', true, true],
            [['test', 'HoomDossier'], 'Hoom', false, true],
            [['test', 'HoomDossier'], 'Hoom', true, true],
            [['test', 'HoomDossier'], 'hoom', true, true],
            [['test', '1 2 3'], '1 2 3', false, true],
            [['test', '1 2 3'], '1', true, true],
            [['test', '1 2 3'], ' ', false, true],
            [['test', '1 2 3aAF4,C&(!#$'], 'af4,', true, true],
            [['test', '1 2 3aAF4,C&(!#$'], 'af4,', false, false],
        ];
    }

    /**
     * @dataProvider arrContainsProvider
     */
    public function testArrContains($array, $needle, $ignoreCase, $expected)
    {
        $this->assertEquals($expected, Str::arrContains($array, $needle, $ignoreCase));
    }

    public static function arrStartsWithProvider()
    {
        return [
            [[], null, false, false],
            [[], 'null', false, false],
            [[], 'null', true, false],
            [[], null, true, false],
            [['test'], null, false, false],
            [['test'], null, true, false],
            [['test'], 'null', false, false],
            [['test'], 'null', true, false],
            [['test'], 'test', false, true],
            [['test', 'hoomdossier'], 'hoom', false, true],
            [['test', 'hoomdossier'], 'Hoom', false, false],
            [['test', 'hoomdossier'], 'Hoom', true, true],
            [['test', 'HoomDossier'], 'Hoom', false, true],
            [['test', 'HoomDossier'], 'Hoom', true, true],
            [['test', 'HoomDossier'], 'hoom', true, true],
            [['test', '1 2 3'], '1 2 3', false, true],
            [['test', '1 2 3'], '1', true, true],
            [['test', '1 2 3'], ' ', false, false],
            [['test', ' 1 2 3'], ' ', false, true],
            [['test', '1 2 3aAF4,C&(!#$'], 'af4,', true, false],
        ];
    }

    /**
     * @dataProvider arrStartsWithProvider
     */
    public function test_arr_starts_with($array, $needle, $ignoreCase, $expected)
    {
        $this->assertEquals($expected, Str::arrStartsWith($array, $needle, $ignoreCase));
    }

    public static function arrKeyStartsWithProvider()
    {
        return [
            [[], null, false, false],
            [[], 'null', false, false],
            [[], 'null', true, false],
            [[], null, true, false],
            [['test'], null, false, false],
            [['test'], null, true, false],
            [['test'], 'null', false, false],
            [['test'], 'null', true, false],
            [['null' => 'test'], 'null', true, true],
            [['null' => 'test'], 'null', false, true],
            [['null' => 'test'], null, false, false],
            [['null' => 'test'], null, true, false],
            [['test'], 'test', false, false],
            [['test', 'hoomdossier'], 'hoom', false, false],
            [['test', 'hoom' => 'hoomdossier'], 'Hoom', false, false],
            [['test', 'hoom' => 'hoomdossier'], 'Hoom', true, true],
            [['test', 'Hoom' => 'hoomdossier'], 'Hoom', false, true],
            [['test', 'Hoom' => 'hoomdossier'], 'Hoom', true, true],
            [['test', 'hoomdossier'], '0', false, false], // Non-string values are not evaluated.
            [['test', 'hoomdossier'], '0', true, false], // The keys of an array are numeric.
            [['test', 'hoomdossier'], 0, true, false], // Therefore, all these fail.
            [['test', 'hoomdossier'], 0, false, false],
            [['test', '1 2 3'], ' ', false, false],
            [['$r' => 'test'], '$r', true, true],
            [['$r' => 'test'], '$r', false, true],
        ];
    }

    /**
     * @dataProvider arrKeyStartsWithProvider
     */
    public function test_arr_key_starts_with($array, $needle, $ignoreCase, $expected)
    {
        $this->assertEquals($expected, Str::arrKeyStartsWith($array, $needle, $ignoreCase));
    }

    public static function htmlArrToDotProvider()
    {
        return [
            ['table[column]', 'table.column'],
            ['table[column][]', 'table.column'],
            ['table', 'table'],
            ['', ''],
        ];
    }

    /**
     * @dataProvider htmlArrToDotProvider
     */
    public function testHtmlArrToDot($value, $expected)
    {
        $this->assertEquals($expected, Str::htmlArrToDot($value));
    }

    public function dotToHtmlProvider()
    {
        return [
            ['', false, ''],
            ['', true, ''],
            [null, false, null],
            [null, true, null],
            ['table', false, 'table'],
            ['table', true, 'table[]'],
            ['table.column', false, 'table[column]'],
            ['table.column', true, 'table[column][]'],
            ['table.json_column.field', false, 'table[json_column][field]'],
            ['table.json_column.field', true, 'table[json_column][field][]'],
            ['table.json_column.field.sub_field', false, 'table[json_column][field][sub_field]'],
            ['table.json_column.field.sub_field', true, 'table[json_column][field][sub_field][]'],
        ];
    }

    /**
     * @dataProvider dotToHtmlProvider
     */
    public function test_dot_to_html($dottedName, $asArray, $expected)
    {
        $this->assertEquals($expected, Str::dotToHtml($dottedName, $asArray));
    }

    public static function hasReplaceablesProvider()
    {
        return [
            ['this is :name', true],
            ['this is a :replacable within', true],
            ['this has no replacable', false],
            ['this has :count :replacables!', true],
        ];
    }

    /**
     * @dataProvider hasReplaceablesProvider
     */
    public function testHasReplaceables($string, $expected)
    {
        $this->assertEquals($expected, Str::hasReplaceables($string));
    }
}
