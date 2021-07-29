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
    public function testHasQuestionOptions($input, $expected)
    {
        $this->assertEquals($expected, QuestionnaireService::hasQuestionOptions($input));
    }

    public function testCreateQuestionnaire()
    {
        $cooperation = factory(Cooperation::class)->create();
        $step = factory(Step::class)->create();

        $questionnaire = QuestionnaireService::createQuestionnaire(
            $cooperation, $step, ['en' => 'Dit is een engelse vertaling', 'nl' => 'Dit is een nederlandse vertaling']
        );

        $this->assertInstanceOf(Questionnaire::class, $questionnaire);
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
        $cooperation = factory(Cooperation::class)->create();

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
}
