<?php

namespace Tests\Feature\app\Services\Scans;

use App\Helpers\Conditions\Clause;
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
use App\Services\Scans\ScanFlowService;
use App\Services\ToolQuestionService;
use Database\Seeders\ScansTableSeeder;
use Database\Seeders\ToolQuestionTypesTableSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ScanFlowServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_conditionals_get_handled_correctly()
    {
        $this->seed(ToolQuestionTypesTableSeeder::class);
        $this->seed(ScansTableSeeder::class);

        $quickScan = Scan::quick();
        $expertScan = Scan::expert();
        // First set up our needed tool structure.
        $quickStep = Step::factory()->create([
            'scan_id' => $quickScan->id,
        ]);
        $otherQuickStep = Step::factory()->create([
            'scan_id' => $quickScan->id,
        ]);
        $expertStep = Step::factory()->create([
            'scan_id' => $expertScan->id,
        ]);

        $count = 3;
        $toolQuestions = ToolQuestion::factory()->count($count)->create();
        $unansweresTq = ToolQuestion::factory()->create();

        $arrayQuestion = ToolQuestion::factory()->typeArray()->create();
        $values = $arrayQuestion->toolQuestionCustomValues()->get();

        $inputType = ToolQuestionType::findByShort('text');
        $arrayType = ToolQuestionType::findByShort('checkbox-icon');

        // SubSteps for first quick step
        $firstStepBaseSubStep = SubStep::factory()->create(['step_id' => $quickStep->id]);
        $firstStepConditionalSubStep = SubStep::factory()->create([
            'step_id' => $quickStep->id,
            'conditions' => [
                [
                    [
                        'column' => $arrayQuestion->short,
                        'clause' => Clause::CONTAINS,
                        'value' => $values[0]->short,
                    ],
                ],
            ],
        ]);
        $firstStepSecondConditionalSubStep = SubStep::factory()->create([
            'step_id' => $quickStep->id,
            'conditions' => [
                [
                    [
                        'column' => $arrayQuestion->short,
                        'clause' => Clause::CONTAINS,
                        'value' => $values[1]->short,
                    ],
                ],
            ],
        ]);

        // SubSteps for second quick step
        $secondStepConditionalSubStep = SubStep::factory()->create([
            'step_id' => $otherQuickStep->id,
            'conditions' => [
                [
                    [
                        'column' => $arrayQuestion->short,
                        'clause' => Clause::CONTAINS,
                        'value' => $values[0]->short,
                    ],
                ],
            ],
        ]);

        // SubSteps for expert step
        $expertStepConditionalSubStep = SubStep::factory()->create([
            'step_id' => $expertStep->id,
            'conditions' => [
                [
                    [
                        'column' => $arrayQuestion->short,
                        'clause' => Clause::CONTAINS,
                        //'value' => $values[0]->short,
                        'value' => $values[2]->short,
                    ],
                ],
            ],
        ]);

        SubSteppable::factory()->createMany([
            // First quick step
            [
                'sub_step_id' => $firstStepBaseSubStep->id,
                'sub_steppable_id' => $arrayQuestion->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $arrayType->id,
            ],
            [
                'sub_step_id' => $firstStepConditionalSubStep->id,
                'sub_steppable_id' => $toolQuestions[0]->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            [
                'sub_step_id' => $firstStepSecondConditionalSubStep->id,
                'sub_steppable_id' => $toolQuestions[1]->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            // Second quick step
            [
                'sub_step_id' => $secondStepConditionalSubStep->id,
                'sub_steppable_id' => $toolQuestions[2]->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
            ],
            [
                'sub_step_id' => $secondStepConditionalSubStep->id,
                'sub_steppable_id' => $unansweresTq->id,
                'sub_steppable_type' => ToolQuestion::class,
                'tool_question_type_id' => $inputType->id,
                'conditions' => [
                    [
                        [
                            // Due to the conditional sub step, this will only show if one AND two are answered.
                            'column' => $arrayQuestion->short,
                            'clause' => Clause::CONTAINS,
                            'value' => $values[1]->short,
                        ],
                    ],
                ],
            ],
            // Expert
            [
                'sub_step_id' => $expertStepConditionalSubStep->id,
                'sub_steppable_id' => $toolQuestions[0]->id,
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

        // This has 3 flow tests:

        // Test 1:
        // We will complete the tool questions beforehand. Then, we will
        // answer the array type with the first value. When we will check conditionals, it will complete the
        // sub steps that require the first value (due to having answers), and so the quick steps will be completed.

        // Test 2:
        // We will change the answer to the second value. This will incomplete the first sub steps of both quick steps.
        // It will then show the second conditional step of the first quick step and since that has an answer,
        // it will be completed. The second step will have no sub steps, and thus will still be complete.

        // Test 3:
        // Finally, we will select BOTH first and second answers. This will complete the first step, as it
        // matches both sub steps. However, the conditionally hidden sub steppable in the second quick step's sub step
        // will now show. Since the tool question attached has no answer, the second quick step will be incompleted.

        // The expert step will be ignored and should not be completed.

        foreach ($toolQuestions as $toolQuestion) {
            DB::table('tool_question_answers')->insert([
                'building_id' => $building->id,
                'input_source_id' => $residentInputSource->id,
                'tool_question_id' => $toolQuestion->id,
                'answer' => "We want to install an old diesel generator.",
            ]);
            DB::table('tool_question_answers')->insert([
                'building_id' => $building->id,
                'input_source_id' => $masterInputSource->id,
                'tool_question_id' => $toolQuestion->id,
                'answer' => "We want to install an old diesel generator.",
            ]);
        }

        $this->assertDatabaseCount('tool_question_answers', $count * 2);
        $this->assertDatabaseCount('completed_sub_steps', 0);
        $this->assertDatabaseCount('completed_steps', 0);

        // Test 1:
        // Save the first answer
        $firstAnswer = [$values[0]->short];
        ToolQuestionService::init()
            ->toolQuestion($arrayQuestion)
            ->building($building)
            ->currentInputSource($residentInputSource)
            ->save($firstAnswer);

        // Create sub step
        CompletedSubStep::create([
            'sub_step_id' => $firstStepBaseSubStep->id,
            'building_id' => $building->id,
            'input_source_id' => $residentInputSource->id,
        ]);

        $flowService = ScanFlowService::init($quickScan, $building, $residentInputSource)
            ->forStep($quickStep);

        $flowService->checkConditionals([$arrayQuestion->short => $firstAnswer], $user);

        // Now, the 2 conditional sub steps should be completed, as they are now viewable, and they have answers. Their
        // steps should now also be completed. Note the double input source savings.
        $this->assertDatabaseCount('completed_steps', 4);
        $this->assertDatabaseCount('completed_sub_steps', 6);
        foreach ([$masterInputSource, $residentInputSource] as $inputSource) {
            // Assert steps
            $this->assertDatabaseHas('completed_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'step_id' => $quickStep->id,
            ]);
            $this->assertDatabaseHas('completed_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'step_id' => $otherQuickStep->id,
            ]);

            // Assert sub steps
            $this->assertDatabaseHas('completed_sub_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'sub_step_id' => $firstStepBaseSubStep->id,
            ]);
            $this->assertDatabaseHas('completed_sub_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'sub_step_id' => $firstStepConditionalSubStep->id,
            ]);
            $this->assertDatabaseHas('completed_sub_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'sub_step_id' => $secondStepConditionalSubStep->id,
            ]);

            // Assert missing sub steps
            $this->assertDatabaseMissing('completed_sub_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'sub_step_id' => $firstStepSecondConditionalSubStep->id,
            ]);

            // Assert expert missing
            $this->assertDatabaseMissing('completed_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'step_id' => $expertStep->id,
            ]);
            $this->assertDatabaseMissing('completed_sub_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'sub_step_id' => $expertStepConditionalSubStep->id,
            ]);
        }

        // Test 2:
        // Save the second answer
        $secondAnswer = [$values[1]->short];
        ToolQuestionService::init()
            ->toolQuestion($arrayQuestion)
            ->building($building)
            ->currentInputSource($residentInputSource)
            ->save($secondAnswer);

        $flowService->checkConditionals([$arrayQuestion->short => $secondAnswer], $user);

        // Now, the first sub steps should have been incompleted, and both steps should be complete.
        $this->assertDatabaseCount('completed_steps', 4);
        $this->assertDatabaseCount('completed_sub_steps', 4);
        foreach ([$masterInputSource, $residentInputSource] as $inputSource) {
            // Assert steps
            $this->assertDatabaseHas('completed_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'step_id' => $quickStep->id,
            ]);
            $this->assertDatabaseHas('completed_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'step_id' => $otherQuickStep->id,
            ]);

            // Assert sub steps
            $this->assertDatabaseHas('completed_sub_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'sub_step_id' => $firstStepBaseSubStep->id,
            ]);
            $this->assertDatabaseHas('completed_sub_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'sub_step_id' => $firstStepSecondConditionalSubStep->id,
            ]);

            // Assert missing sub steps
            $this->assertDatabaseMissing('completed_sub_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'sub_step_id' => $firstStepConditionalSubStep->id,
            ]);
            $this->assertDatabaseMissing('completed_sub_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'sub_step_id' => $secondStepConditionalSubStep->id,
            ]);

            // Assert expert missing
            $this->assertDatabaseMissing('completed_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'step_id' => $expertStep->id,
            ]);
            $this->assertDatabaseMissing('completed_sub_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'sub_step_id' => $expertStepConditionalSubStep->id,
            ]);
        }

        // Test 3:
        // Save both answers
        $bothAnswers = [$values[0]->short, $values[1]->short];
        ToolQuestionService::init()
            ->toolQuestion($arrayQuestion)
            ->building($building)
            ->currentInputSource($residentInputSource)
            ->save($bothAnswers);

        $flowService->checkConditionals([$arrayQuestion->short => $bothAnswers], $user);

        // Now, everything in the first quick step should be complete, and everything in the second quick step should
        // be incomplete.
        $this->assertDatabaseCount('completed_steps', 2);
        $this->assertDatabaseCount('completed_sub_steps', 6);
        foreach ([$masterInputSource, $residentInputSource] as $inputSource) {
            // Assert steps
            $this->assertDatabaseHas('completed_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'step_id' => $quickStep->id,
            ]);

            // Assert missing steps
            $this->assertDatabaseMissing('completed_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'step_id' => $otherQuickStep->id,
            ]);

            // Assert sub steps
            $this->assertDatabaseHas('completed_sub_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'sub_step_id' => $firstStepBaseSubStep->id,
            ]);
            $this->assertDatabaseHas('completed_sub_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'sub_step_id' => $firstStepConditionalSubStep->id,
            ]);
            $this->assertDatabaseHas('completed_sub_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'sub_step_id' => $firstStepSecondConditionalSubStep->id,
            ]);

            // Assert missing sub steps
            $this->assertDatabaseMissing('completed_sub_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'sub_step_id' => $secondStepConditionalSubStep->id,
            ]);

            // Assert expert missing
            $this->assertDatabaseMissing('completed_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'step_id' => $expertStep->id,
            ]);
            $this->assertDatabaseMissing('completed_sub_steps', [
                'building_id' => $building->id,
                'input_source_id' => $inputSource->id,
                'sub_step_id' => $expertStepConditionalSubStep->id,
            ]);
        }
    }
}