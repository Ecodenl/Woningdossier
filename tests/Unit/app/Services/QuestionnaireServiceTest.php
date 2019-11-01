<?php

namespace Tests\Unit\app\Services;

use App\Models\Cooperation;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Models\User;
use App\Services\QuestionnaireService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuestionnaireServiceTest extends TestCase
{
    use RefreshDatabase;
    public function setUp()
    {
        parent::setUp();
        $this->refreshDatabase();
        $this->seed();
    }

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
            [['fr' => '', 'en' => null, 'nl' => '',],  true,],
            [['fr' => null, 'en' => null, 'nl' => '', 'de' => 'duitse tekst'],  false,],
        ];
    }

    /**
     * @dataProvider isEmptyTranslationProvider
     */
    public function testIsEmptyTranslation($translations, $expected)
    {
        $this->assertEquals($expected, QuestionnaireService::isEmptyTranslation($translations));
    }

    public function createQuestionProvider()
    {


//        return [
//            [
//                Questionnaire::find(1),
//                [
//                    'question' => [
//                        'nl' => 'Dit is een kort antwoord'
//                    ],
//                    'type' => 'text',
//                    'guid' => com_create_guid()
//                ]
//            ]
//        ];
    }


    public function testCreateQuestionnaire()
    {
        $cooperation = Cooperation::find(1);
        $step = Step::find(1);
        QuestionnaireService::createQuestionnaire(        $cooperation,
            $step,
            ['en' => 'Dit is een engelse vertaling', 'nl' => 'Dit is een nederlandse vertaling',]);

        $this->assertEquals(1, Questionnaire::count());
    }

    public function testCreateQuestion()
    {
        $this->assertEquals(1, User::all()->count());
//        $this->refreshDatabase();
//        $questionnaire, $questionData, $questionType, $validation, $order
//        $this->assertDatabaseHas('cooperations');
    }
}
