<?php

namespace App\Console\Commands\Upgrade\Merge;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\Str;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixCompletedSubSteps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:fix-completed-sub-steps';

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
        $buildingIds = DB::table('cooperations')
            ->select('buildings.id')
            ->leftJoin('users', 'users.cooperation_id', '=', 'cooperations.id')
            ->leftJoin('buildings', 'users.id', '=', 'buildings.user_id')
            ->whereIn('cooperations.slug', ['deltawind', 'duec', 'lochemenergie'])
            ->pluck('id')->toArray();

        Schema::disableForeignKeyConstraints();

        DB::table('sub_steps')->where('id', 33)->update(['id' => 5]);

        // Correct incorrect sub step data
        DB::table('completed_sub_steps')
            ->where('sub_step_id', '=', 0)
            ->whereIn('building_id', $buildingIds)
            ->update([
                'sub_step_id' => 30,
            ]);

        DB::table('completed_sub_steps')
            ->where('sub_step_id', '=', 33)
            ->whereIn('building_id', $buildingIds)
            ->update([
                'sub_step_id' => 5,
            ]);

        DB::table('completed_sub_steps')
            ->where('sub_step_id', '=', 32)
            ->whereIn('building_id', $buildingIds)
            ->update([
                'sub_step_id' => 1,
            ]);

        DB::table('completed_sub_steps')
            ->where('sub_step_id', '=', 5)
            ->whereIn('building_id', $buildingIds)
            ->where('created_at', '>=', '2021-11-04')
            ->update([
                'sub_step_id' => 31,
            ]);

        Schema::enableForeignKeyConstraints();

        $buildings = Building::whereIn('id', $buildingIds)->get();
        $inputSources = InputSource::findByShorts([
            InputSource::RESIDENT_SHORT,
            InputSource::COACH_SHORT,
        ]);
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $subSteps = SubStep::all();

        // Now check all sub steps for the buildings
        $bar = $this->output->createProgressBar($buildings->count());
        $bar->start();
        /** @var Building $building */
        foreach ($buildings as $building) {
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

                    $existing = DB::table('completed_sub_steps')
                        ->where('sub_step_id', $subStep->id)
                        ->where('building_id', $building->id)
                        ->where('input_source_id', $inputSource->id)
                        ->first();

                    if ($completeStep) {
                        // Doesn't exist but it should!
                        if (! $existing instanceof \stdClass) {
                            // insert he completed sub step
                            $subStepCreated = DB::table('completed_sub_steps')->insert([
                                'sub_step_id' => $subStep->id,
                                'building_id' => $building->id,
                                'input_source_id' => $inputSource->id,
                                'created_at' => now(),
                            ]);

                            if ($subStepCreated) {
                                $masterExisting = DB::table('completed_sub_steps')
                                    ->where('sub_step_id', $subStep->id)
                                    ->where('building_id', $building->id)
                                    ->where('input_source_id', $masterInputSource->id)
                                    ->first();

                                if (! $masterExisting instanceof \stdClass) {
                                    DB::table('completed_sub_steps')->insert([
                                        'sub_step_id' => $subStep->id,
                                        'building_id' => $building->id,
                                        'input_source_id' => $masterInputSource->id,
                                        'created_at' => now(),
                                    ]);
                                }
                            }
                        }
                    } elseif ($existing instanceof \stdClass) {
                        // It does exist but it shouldn't.
                        DB::table('completed_sub_steps')->where('id', $existing->id)->delete();

                        $totalLeft = DB::table('completed_sub_steps')
                            ->where('sub_step_id', $subStep->id)
                            ->where('building_id', $building->id)
                            ->count();

                        // If only one left, it's the master, no doubt. We delete it too.
                        if ($totalLeft == 1) {
                            $masterExisting = DB::table('completed_sub_steps')
                                ->where('sub_step_id', $subStep->id)
                                ->where('building_id', $building->id)
                                ->where('input_source_id', $masterInputSource->id)
                                ->first();

                            if ($masterExisting instanceof \stdClass) {
                                DB::table('completed_sub_steps')->where('id', $masterExisting->id)->delete();
                            }
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
}