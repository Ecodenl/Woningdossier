<?php

namespace Tests\Unit\app\Services;

use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Services\QuestionnaireService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\CreatesApplication;
use Tests\TestCase;

class QuestionnaireServiceTest extends TestCase
{
    use CreatesApplication;
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public static function hasQuestionOptionsProvider()
    {
        return [
            ['select', true],
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
            [['en' => 'Dit is een engelse vertaling', 'nl' => 'Dit is een nederlandse vertaling'], 'Dit is een engelse vertaling'],
            [['en' => '', 'nl' => 'Dit is een nederlandse vertaling'], 'Dit is een nederlandse vertaling'],
            [['fr' => 'franse vertaling', 'en' => '', 'nl' => null], 'franse vertaling'],
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
            [['en' => 'Dit is een engelse vertaling', 'nl' => 'Dit is een nederlandse vertaling'], false],
            [['en' => '', 'nl' => 'Dit is een nederlandse vertaling'], false],
            [['fr' => 'franse vertaling', 'en' => '', 'nl' => null], false],
            [['fr' => '', 'en' => '', 'nl' => ''], true],
            [['fr' => '', 'en' => null, 'nl' => ''], true],
            [['fr' => null, 'en' => null, 'nl' => '', 'de' => 'duitse tekst'], false],
        ];
    }

    /**
     * @dataProvider isEmptyTranslationProvider
     */
    public function testIsEmptyTranslation($translations, $expected)
    {
        $this->assertEquals($expected, QuestionnaireService::isEmptyTranslation($translations));
    }

    public function testCreateQuestionnaire()
    {
        $cooperation = Cooperation::find(1);
        $step = Step::find(1);
        QuestionnaireService::createQuestionnaire(
            $cooperation, $step, ['en' => 'Dit is een engelse vertaling', 'nl' => 'Dit is een nederlandse vertaling']
        );

        $this->assertEquals(1, Questionnaire::count());
    }

    public function testCreateQuestionProvider()
    {
        return [
            [[
                'question' => [
                    'nl' => 'Test questionnaire',
                ],
                'required' => true,
            ]],
        ];
    }

    /**
     * @dataProvider testCreateQuestionProvider
     */
    public function testCreateQuestion($questionData)
    {
        // first we need to create a questionnaire with a question
        $questionnaire = factory(Questionnaire::class)->create();

        QuestionnaireService::createQuestion($questionnaire, $questionData, 'text', [], 0);

        $this->assertDatabaseHas('questions', [
            'questionnaire_id' => $questionnaire->id,
        ]);
    }

    public function testCopyQuestionnaireToCooperation()
    {
        // first we need to create a questionnaire with a question
        $questionnaire = factory(Questionnaire::class)->create();
        for ($i = 0; $i < 10; ++$i) {
            $questionnaire->questions()->save(
                factory(Question::class)->make(['order' => $i])
            );
        }
        // where we will copy the questionnaire to.
        $cooperation = Cooperation::whereSlug('hnwr')->first();

        // copy the questionnaire
        QuestionnaireService::copyQuestionnaireToCooperation($cooperation, $questionnaire);

        // check if the questionnaire is copied
        $this->assertDatabaseHas('questionnaires', [
            'cooperation_id' => $cooperation->id,
        ]);
        $this->assertCount(1, Questionnaire::forMyCooperation($cooperation->id)->get());

        // check if questions have been copied
        $copiedQuestionnaire = Questionnaire::forMyCooperation($cooperation->id)->first();

        // check if the translations are the same
        $this->assertSame($copiedQuestionnaire->name, $questionnaire->name);

        // now check if the uuid's are different
        // this is important, otherwise the translations will run trough each other
        $this->assertNotSame($copiedQuestionnaire->attributesToArray()['name'], $questionnaire->attributesToArray()['name']);

        $this->assertDatabaseHas('questions', [
            'questionnaire_id' => $copiedQuestionnaire->id,
        ]);
    }
}
