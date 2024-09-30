<?php

namespace Tests\Unit\app\Services;

use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Services\QuestionnaireService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionnaireServiceTest extends TestCase
{
    use RefreshDatabase;

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
    public function testHasQuestionOptions($input, $expected): void
    {
        $this->assertEquals($expected, QuestionnaireService::hasQuestionOptions($input));
    }

    public function createQuestionProvider()
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
     * @dataProvider createQuestionProvider
     */
    public function testCreateQuestion($questionData): void
    {
        // first we need to create a questionnaire with a question
        $questionnaire = Questionnaire::factory()->create();

        QuestionnaireService::createQuestion($questionnaire, $questionData, 'text', [], 0);

        $this->assertDatabaseHas('questions', [
            'questionnaire_id' => $questionnaire->id,
        ]);
    }

    public function testCopyQuestionnaireToCooperation(): void
    {
        // first we need to create a questionnaire with a question
        $questionnaire = Questionnaire::factory()->create();
        for ($i = 0; $i < 10; ++$i) {
            $questionnaire->questions()->save(
                Question::factory()->make(['order' => $i])
            );
        }
        // where we will copy the questionnaire to.
        $cooperation = Cooperation::factory()->create();

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

        $this->assertDatabaseHas('questions', [
            'questionnaire_id' => $copiedQuestionnaire->id,
        ]);
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
    public function testIsEmptyTranslation($translations, $expected): void
    {
        $this->assertEquals($expected, QuestionnaireService::isEmptyTranslation($translations));
    }

    public function isNotEmptyTranslationProvider()
    {
        return [
            [['en' => 'Dit is een engelse vertaling', 'nl' => 'Dit is een nederlandse vertaling'], true],
            [['en' => '', 'nl' => 'Dit is een nederlandse vertaling'], true],
            [['fr' => 'franse vertaling', 'en' => '', 'nl' => null], true],
            [['fr' => '', 'en' => '', 'nl' => ''], false],
            [['fr' => '', 'en' => null, 'nl' => ''], false],
            [['fr' => null, 'en' => null, 'nl' => '', 'de' => 'duitse tekst'], true],
        ];
    }

    /**
     * @dataProvider isNotEmptyTranslationProvider
     */
    public function testisNotEmptyTranslation($translations, $expected): void
    {
        $this->assertEquals($expected, QuestionnaireService::isNotEmptyTranslation($translations));
    }
}
