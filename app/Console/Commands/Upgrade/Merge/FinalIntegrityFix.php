<?php

namespace App\Console\Commands\Upgrade\Merge;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\StepHelper;
use App\Helpers\Str;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FinalIntegrityFix extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:final-integrity-fix';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Corrects the data that got messed up trying to correct the data.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Map all OLD steps to new steps

        $stepMap = [
            'building-characteristics' => 'building-data',
            'current-state' => 'residential-status',
            'usage' => 'usage-quick-scan',
            'interest' => 'living-requirements',
        ];

        $this->info('Starting old mapping (general data to quickscan)');
        $bar = $this->output->createProgressBar(4);
        $bar->start();

        foreach ($stepMap as $fromStepShort => $toStepShort) {
            $fromStep = DB::table('steps')->where('short', '=', $fromStepShort)->first();
            $toStep = DB::table('steps')->where('short', '=', $toStepShort)->first();

            // Map completed steps to new steps
            DB::table('completed_steps')->where('step_id', '=', $fromStep->id)
                ->update([
                    'step_id' => $toStep->id,
                    'updated_at' => now(),
                ]);

            // Get the now swapped steps
            $completedSteps = DB::table('completed_steps')
                ->where('step_id', '=', $toStep->id)
                ->cursor();

            // Get all sub steps for this step
            $subStepsForStep = DB::table('sub_steps')
                ->where('step_id', '=', $toStep->id)
                ->get();

            foreach ($completedSteps as $completedStep) {
                // Set each sub step as complete
                foreach ($subStepsForStep as $subStep) {
                    DB::table('completed_sub_steps')->updateOrInsert([
                        'sub_step_id' => $subStep->id,
                        'building_id' => $completedStep->building_id,
                        'input_source_id' => $completedStep->input_source_id,
                    ], [
                        'updated_at' => now(),
                    ]);
                }
            }
            $bar->advance();
        }
        $bar->finish();
        $this->output->newLine();


        $stepIds = DB::table('steps')
            ->whereIn('short', $stepMap)
            ->pluck('id')->toArray();

        // Re-use step map for the values
        $buildingIdsFromCompletedSteps = DB::table('completed_steps')
            ->distinct()
            ->whereIn('step_id', $stepIds)
            ->pluck('building_id')->toArray();

        $buildings = Building::whereNotIn('id', $buildingIdsFromCompletedSteps)
            ->get();

        $this->info('Starting new mapping (valid answers to (sub) steps)');
        $bar = $this->output->createProgressBar($buildings->count());
        $bar->start();

        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $inputSources = InputSource::findByShorts([
            InputSource::RESIDENT_SHORT,
            InputSource::COACH_SHORT,
        ]);
        $subSteps = SubStep::all();
        /** @var Building $building */
        foreach ($buildings as $building) {
            if (!$building instanceof Building){
                continue;
            }
            foreach ($inputSources as $inputSource) {
                foreach ($subSteps as $subStep) {
                    $completeStep = true;
                    foreach ($subStep->toolQuestions as $toolQuestion) {
                        // If a question is for a specific input source, we won't be able to get an answer for it
                        if (is_null($toolQuestion->for_specific_input_source)
                            || $inputSource->id === $toolQuestion->for_specific_input_source
                        ) {
                            // A non-required question could be not answered but that shouldn't matter anyway
                            if (in_array('required', $toolQuestion->validation)
                                || Str::arrContains($toolQuestion->validation, 'required_if', true)
                            ) {
                                // Check the actual answer. If one answer is not filled, we can't complete it.
                                if (is_null($building->getAnswer($inputSource, $toolQuestion))) {
                                    // Conditions could not be met, time to check...
                                    if (! empty($toolQuestion->conditions)) {
                                        $answers = [];

                                        foreach ($toolQuestion->conditions as $conditionSet) {
                                            foreach ($conditionSet as $condition) {
                                                $otherSubStepToolQuestion = ToolQuestion::where('short', $condition['column'])->first();
                                                if ($otherSubStepToolQuestion instanceof ToolQuestion) {
                                                    $otherSubStepAnswer = $building->getAnswer($inputSource,
                                                        $otherSubStepToolQuestion);

                                                    $answers[$otherSubStepToolQuestion->short] = $otherSubStepAnswer;
                                                }
                                            }
                                        }

                                        $evaluatableAnswers = collect($answers);

                                        // Evaluation did not pass. We continue to the next tool question
                                        if (! ConditionEvaluator::init()->evaluateCollection($toolQuestion->conditions,
                                            $evaluatableAnswers)
                                        ) {
                                            continue;
                                        }
                                    }

                                    $completeStep = false;
                                    // No point in checking the other tool questions if we're not completing it anyway
                                    break;
                                }
                            }
                        }
                    }

                    if ($completeStep) {
                        // insert he completed sub step
                        $subStepCreated = DB::table('completed_sub_steps')->insert([
                            'sub_step_id' => $subStep->id,
                            'building_id' => $building->id,
                            'input_source_id' => $inputSource->id
                        ]);

                        $masterStep = DB::table('completed_sub_steps')
                            ->where('sub_step_id', $subStep->id)
                            ->where('building_id', $building->id)
                            ->where('input_source_id', $masterInputSource->id)
                            ->first();

                        if (! $masterStep instanceof \stdClass) {
                            DB::table('completed_sub_steps')->insert([
                                'sub_step_id' => $subStep->id,
                                'building_id' => $building->id,
                                'input_source_id' => $masterInputSource->id
                            ]);
                        }

                        if ($subStepCreated) {
                            // hydrate the model, this way we can use the observer code we actually need.
                            $completeSubStep = CompletedSubStep::hydrate([[
                                'sub_step_id' => $subStep->id,
                                'building_id' => $building->id,
                                'input_source_id' => $inputSource->id
                            ]])->first();
                            $this->completedSubStepObserverSaved($completeSubStep);
                        }
                    }
                }
            }
            $bar->advance();
        }
        $bar->finish();
        $this->output->newLine();

        return 0;
    }

    /**
     * This code is the same as the CompletedSubStepObserver, but without the events for recalc etc.
     *
     * @param $completedSubStep
     */
    private function completedSubStepObserverSaved($completedSubStep)
    {
        // Check if this sub step finished the step
        $subStep = $completedSubStep->subStep;

        if ($subStep instanceof SubStep) {
            $step = $subStep->step;
            $inputSource = $completedSubStep->inputSource;
            $building = $completedSubStep->building;

            if ($step instanceof Step && $inputSource instanceof InputSource && $building instanceof Building) {
                $allCompletedSubStepIds = CompletedSubStep::forInputSource($inputSource)
                    ->forBuilding($building)
                    ->whereHas('subStep', function ($query) use ($step) {
                        $query->where('step_id', $step->id);
                    })
                    ->pluck('sub_step_id')->toArray();

                $allSubStepIds = $step->subSteps()->pluck('id')->toArray();

                $diff = array_diff($allSubStepIds, $allCompletedSubStepIds);

                if (empty ($diff)) {
                    // The sub step that has been completed finished up the set, so we complete the main step
                    StepHelper::complete($step, $building, $inputSource);
                } else {
                    // We didn't fill in each sub step. But, it might be that there's sub steps with conditions
                    // that we didn't get. Let's check
                    $leftoverSubSteps = SubStep::findMany($diff);

                    $cantSee = 0;
                    foreach ($leftoverSubSteps as $subStep) {
                        $canShowSubStep = ConditionEvaluator::init()
                            ->building($building)
                            ->inputSource($inputSource)
                            ->evaluate($subStep->conditions ?? []);

                        if (!$canShowSubStep) {
                            ++$cantSee;
                        }
                    }

                    if ($cantSee === $leftoverSubSteps->count()) {
                        // Conditions "passed", so we complete!
                        StepHelper::complete($step, $building, $inputSource);
                    }
                }
            }
        }
    }
}