<?php

namespace Tests\Feature\app\Jobs;

use App\Models\Account;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\SubSteppable;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionType;
use App\Models\User;
use Database\Seeders\ScansTableSeeder;
use Database\Seeders\ToolQuestionTypesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CompleteRelatedSubStepTest extends TestCase
{
    use RefreshDatabase;

    public function test_related_sub_steps_get_completed(): void
    {
        $this->seed(ToolQuestionTypesTableSeeder::class);
        $this->seed(ScansTableSeeder::class);

        // First set up our needed tool structure.
        $quickScan = Scan::quick();
        $expertScan = Scan::expert();
        $quickStep = Step::factory()->create([
            'scan_id' => $quickScan->id,
        ]);
        $expertStep = Step::factory()->create([
            'scan_id' => $expertScan->id,
        ]);
        $baseSubStep = SubStep::factory()->create(['step_id' => $quickStep->id]);
        $completableRelatedSubStep = SubStep::factory()->create(['step_id' => $quickStep->id]);
        $incompletableRelatedSubStep = SubStep::factory()->create(['step_id' => $quickStep->id]);
        $completableSubRelatedSubStep = SubStep::factory()->create(['step_id' => $quickStep->id]);
        $incompletableSubRelatedSubStep = SubStep::factory()->create(['step_id' => $quickStep->id]);
        $completableExpertRelatedSubStep = SubStep::factory()->create(['step_id' => $expertStep->id]);

        $firstCompletedTq = ToolQuestion::factory()->create();
        $secondCompletedTq = ToolQuestion::factory()->create();
        $randomTq = ToolQuestion::factory()->create();
        $anotherRandomTq = ToolQuestion::factory()->create();

        $inputType = ToolQuestionType::findByShort('text');

        SubSteppable::factory()->createMany([
            // Base sub step will have one tool question, one to complete.
            [
                'sub_step_id' => $baseSubStep->id,
                'sub_steppable_id' => $firstCompletedTq->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            // Related completable will have two, one will be completed.
            [
                'sub_step_id' => $completableRelatedSubStep->id,
                'sub_steppable_id' => $firstCompletedTq->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            [
                'sub_step_id' => $completableRelatedSubStep->id,
                'sub_steppable_id' => $secondCompletedTq->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            // Incompletable will have two, one uncompleted.
            [
                'sub_step_id' => $incompletableRelatedSubStep->id,
                'sub_steppable_id' => $firstCompletedTq->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            [
                'sub_step_id' => $incompletableRelatedSubStep->id,
                'sub_steppable_id' => $randomTq->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            // Related sub completable will have one, already completed.
            [
                'sub_step_id' => $completableSubRelatedSubStep->id,
                'sub_steppable_id' => $secondCompletedTq->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            // Related sub incompletable will have two, one completed, one uncomplete.
            [
                'sub_step_id' => $incompletableSubRelatedSubStep->id,
                'sub_steppable_id' => $secondCompletedTq->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            [
                'sub_step_id' => $incompletableSubRelatedSubStep->id,
                'sub_steppable_id' => $anotherRandomTq->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            // Expert sub steppable, will be ignored
            [
                'sub_step_id' => $completableExpertRelatedSubStep->id,
                'sub_steppable_id' => $firstCompletedTq->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
        ]);

        // Create user.
        $cooperation = Cooperation::factory()->create();

        $account = Account::factory()->create();
        $user = User::factory()->create([
            'cooperation_id' => $cooperation->id,
            'account_id' => $account->id,
        ]);
        $building = Building::factory()->create(['user_id' => $user->id]);

        // Create required extra data.
        InputSource::factory()->createMany([
            ['short' => InputSource::RESIDENT_SHORT],
            ['short' => InputSource::MASTER_SHORT],
        ]);

        $residentInputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        // Now onto the test case...
        // First complete the 2 tool questions
        foreach ([$residentInputSource, $masterInputSource] as $inputSource) {
            DB::table('tool_question_answers')->insert([
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'tool_question_id' => $firstCompletedTq->id,
                'answer' => "Nuclear energy is better than wind and solar combined.",
            ]);
            DB::table('tool_question_answers')->insert([
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'tool_question_id' => $secondCompletedTq->id,
                'answer' => "My comment is longer than yours!",
            ]);
        }

        // Assert properly inserted.
        // 4 answers, 1 for each input source.
        $this->assertDatabaseCount('tool_question_answers', 4);
        $this->assertDatabaseCount('completed_sub_steps', 0);

        // Now complete the first sub step, which would normally happen after saving the question.
        CompletedSubStep::create([
            'sub_step_id' => $baseSubStep->id,
            'building_id' => $building->id,
            'input_source_id' => $residentInputSource->id
        ]);

        // By saving the tool question, it should have completed 3 sub steps in total, meaning 6 in the database
        // due to master input source.
        $this->assertDatabaseCount('completed_sub_steps', 6);

        // Assert correct data for each input source.
        foreach ([$residentInputSource, $masterInputSource] as $inputSource) {
            $this->assertDatabaseHas('completed_sub_steps', [
                'sub_step_id' => $baseSubStep->id,
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ]);
            $this->assertDatabaseHas('completed_sub_steps', [
                'sub_step_id' => $completableRelatedSubStep->id,
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ]);
            $this->assertDatabaseHas('completed_sub_steps', [
                'sub_step_id' => $completableSubRelatedSubStep->id,
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ]);
            $this->assertDatabaseMissing('completed_sub_steps', [
                'sub_step_id' => $incompletableRelatedSubStep->id,
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ]);
            $this->assertDatabaseMissing('completed_sub_steps', [
                'sub_step_id' => $incompletableSubRelatedSubStep->id,
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ]);
            $this->assertDatabaseMissing('completed_sub_steps', [
                'sub_step_id' => $completableExpertRelatedSubStep->id,
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
            ]);
        }
    }
}