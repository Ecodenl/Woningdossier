<?php

namespace Tests\Unit\app\Services;

use PHPUnit\Framework\Attributes\DataProvider;
use App\Models\Cooperation;
use App\Models\Question;
use App\Models\Questionnaire;
use App\Models\Step;
use App\Services\QuestionnaireService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class QuestionnaireServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testCopyQuestionnaireToCooperation(): void
    {
        // first we need to create a questionnaire with a question
        $questionnaire = Questionnaire::factory()->withCooperation()->create();
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
}
