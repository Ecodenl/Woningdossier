<?php

namespace Tests\Feature\app\Jobs;

use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\SubSteppable;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionType;
use App\Models\User;
use App\Services\ToolQuestionService;
use Database\Seeders\ToolQuestionTypesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CompleteRelatedSubStepTest extends TestCase
{
    use WithFaker,
        RefreshDatabase;

    public $seed = true;
    public $seeder = ToolQuestionTypesTableSeeder::class;

    public function test_related_sub_steps_get_completed()
    {
        // First set up our needed tool structure.
        $step = Step::factory()->create();
        $baseSubStep = SubStep::factory()->create(['step_id' => $step->id]);
        $completableRelatedSubStep = SubStep::factory()->create(['step_id' => $step->id]);
        $incompletableRelatedSubStep = SubStep::factory()->create(['step_id' => $step->id]);
        $completableSubRelatedSubStep = SubStep::factory()->create(['step_id' => $step->id]);
        $incompletableSubRelatedSubStep = SubStep::factory()->create(['step_id' => $step->id]);

        $tqToComplete = ToolQuestion::factory()->create();
        $completedTq = ToolQuestion::factory()->create();
        $randomTq = ToolQuestion::factory()->create();
        $anotherRandomTq = ToolQuestion::factory()->create();

        $inputType = ToolQuestionType::findByShort('text');

        SubSteppable::factory()->createMany([
            // Base sub step will have one tool question, one to complete.
            [
                'sub_step_id' => $baseSubStep->id,
                'sub_steppable_id' => $tqToComplete->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            // Related completable will have two, one will be completed.
            [
                'sub_step_id' => $completableRelatedSubStep->id,
                'sub_steppable_id' => $tqToComplete->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            [
                'sub_step_id' => $completableRelatedSubStep->id,
                'sub_steppable_id' => $completedTq->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            // Incompletable will have two, one uncompleted.
            [
                'sub_step_id' => $incompletableRelatedSubStep->id,
                'sub_steppable_id' => $tqToComplete->id,
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
                'sub_steppable_id' => $completedTq->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            // Related sub incompletable will have two, one completed, one uncomplete.
            [
                'sub_step_id' => $incompletableSubRelatedSubStep->id,
                'sub_steppable_id' => $completedTq->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            [
                'sub_step_id' => $baseSubStep->id,
                'sub_steppable_id' => $anotherRandomTq->id,
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
        // First manually complete the $completedTq.
        foreach ([$residentInputSource, $masterInputSource] as $inputSource) {
            DB::table('tool_question_answers')->insert([
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'tool_question_id' => $completedTq->id,
                'answer' => "Nuclear energy is better than wind and solar combined.",
            ]);
        }

        // Assert properly inserted.
        $this->assertDatabaseCount('tool_question_answers', 2);
        $this->assertDatabaseCount('completed_sub_steps', 0);

        // Now, we will test that if the user completed a tool question, it completes related sub steps.
        ToolQuestionService::init($tqToComplete)
            ->building($building)
            ->currentInputSource($residentInputSource)
            ->save('My comment is longer than yours!');

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
        }
    }
}