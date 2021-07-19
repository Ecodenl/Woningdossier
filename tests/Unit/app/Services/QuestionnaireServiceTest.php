<?php

namespace Tests\Unit\app\Services;

use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Services\QuestionnaireService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\CreatesApplication;
use Tests\TestCase;

class QuestionnaireServiceTest extends TestCase
{
    use RefreshDatabase;

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
        $oldCount = Questionnaire::count();

        $cooperation = factory(Cooperation::class)->create();
        $this->seed(\StepsTableSeeder::class);

        $step = Step::inRandomOrder()->first();
        QuestionnaireService::createQuestionnaire(
            $cooperation, $step, ['en' => 'Dit is een engelse vertaling', 'nl' => 'Dit is een nederlandse vertaling']
        );


        $this->assertEquals($oldCount + 1, Questionnaire::count());
    }

    public function CreateQuestionProvider()
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
     * @dataProvider CreateQuestionProvider
     */
    public function testCreateQuestion($questionData)
    {
        // first we need to create a questionnaire with a question
        $cooperation = factory(Cooperation::class)->create();
        $questionnaire = factory(Questionnaire::class)->create(['cooperation_id' => $cooperation->id]);

        QuestionnaireService::createQuestion($questionnaire, $questionData, 'text', [], 0);

        $this->assertDatabaseHas('questions', [
            'questionnaire_id' => $questionnaire->id,
        ]);
    }

    public function CreateQuestionWithOptionProvider()
    {
        $uuid = (string) \Ramsey\Uuid\Uuid::uuid4();

        return [
            [[
                'question' => [
                    'nl' => 'Test questionnaire',
                ],
                'required' => true,
                'options' => [
                    $uuid => [
                        'nl' => 'Test option',
                    ],
                ],
            ]],
        ];
    }

    /**
     * @dataProvider CreateQuestionWithOptionProvider
     */
    public function testCreateQuestionWithOption($questionData)
    {
        // first we need to create a questionnaire with a question
        $cooperation = factory(Cooperation::class)->create();
        $questionnaire = factory(Questionnaire::class)->create(['cooperation_id' => $cooperation->id]);

        QuestionnaireService::createQuestion($questionnaire, $questionData, 'select', [], 0);

        $this->assertDatabaseHas('questions', [
            'questionnaire_id' => $questionnaire->id,
        ]);

        $question = Question::where('questionnaire_id', $questionnaire->id)->latest()->first();

        $this->assertDatabaseHas('question_options', [
            'question_id' => $question->id,
        ]);
    }

    public function testCopyQuestionnaireToCooperation()
    {
        // first we need to create a questionnaire with a question
        $cooperation = factory(Cooperation::class)->create();
        $questionnaire = factory(Questionnaire::class)->create(['cooperation_id' => $cooperation->id]);
        for ($i = 0; $i < 10; ++$i) {
            $questionnaire->questions()->save(
                factory(Question::class)->make(['order' => $i])
            );
        }

        // test if the initial questionnaire has been created
        $this->assertDatabaseHas('questionnaires', [
            'cooperation_id' => $cooperation->id,
        ]);

        $cooperationToCopyTo = factory(Cooperation::class)->create();
        $this->assertDatabaseMissing('questionnaires', [
            'cooperation_id' => $cooperationToCopyTo->id,
        ]);

        // copy the questionnaire
        QuestionnaireService::copyQuestionnaireToCooperation($cooperationToCopyTo, $questionnaire);

        $this->assertDatabaseHas('questionnaires', [
            'cooperation_id' => $cooperationToCopyTo->id,
        ]);

        $copiedQuestionnaire = Questionnaire::forMyCooperation($cooperationToCopyTo->id)->first();

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
