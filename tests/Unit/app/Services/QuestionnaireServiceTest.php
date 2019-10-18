<?php

namespace Tests\Unit\app\Services;

use App\Services\QuestionnaireService;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuestionnaireServiceTest extends TestCase
{
    public static function hasQuestionOptionsProvider()
    {
        return [
            ['select',  true],
            ['radio', true],
            ['checkbox', true],
            ['text', false],
            ['input', false],
            ['date', false],
        ];
    }

    /**
     * @dataProvider hasQuestionOptionsProvider
     */
    public function testHasQuestionOptions($input, $expected)
    {
        $this->assertEquals($expected, QuestionnaireService::hasQuestionOptions($input));
    }

    public static function getTranslationProvider()
    {
       return [
           [['en' => 'Dit is een engelse vertaling', 'nl' => 'Dit is een nederlandse vertaling',], 'Dit is een engelse vertaling'],
           [['en' => '', 'nl' => 'Dit is een nederlandse vertaling',], 'Dit is een nederlandse vertaling',],
           [['fr' => 'franse vertaling', 'en' => '', 'nl' => null,],  'franse vertaling',],
       ];
    }

    /**
     * @dataProvider getTranslationProvider
     */
    public function testGetTranslation($translations, $expected)
    {
        $this->assertEquals($expected, QuestionnaireService::getTranslation($translations, $expected));
    }

    public function isEmptyTranslationProvider()
    {
        return [
            [['en' => 'Dit is een engelse vertaling', 'nl' => 'Dit is een nederlandse vertaling',], false],
            [['en' => '', 'nl' => 'Dit is een nederlandse vertaling',], false,],
            [['fr' => 'franse vertaling', 'en' => '', 'nl' => null,],  false,],
            [['fr' => '', 'en' => '', 'nl' => '',],  true,],
        ];
    }

    /**
     * @dataProvider isEmptyTranslationProvider
     */
    public function testIsEmptyTranslation($translations, $expected)
    {
        $this->assertEquals($expected, QuestionnaireService::isEmptyTranslation($translations));
    }
}
