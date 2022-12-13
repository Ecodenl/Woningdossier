<?php

namespace App\Console\Commands\Upgrade\HeatPump\WaterCalc;

use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateToolQuestions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:heat-pump:water-calc:update-tool-questions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the tool questions in the format for the heat pump water calculation change';

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
     * @return mixed
     */
    public function handle()
    {
        $buildingsToRecalculate = [];

        $relevantInputSources = DB::table('input_sources')->get();

        $heatSourceQuestion = ToolQuestion::findByShort('heat-source');
        $heatSourceQuestionOther = ToolQuestion::findByShort('heat-source-other');
        $heatSourceWaterQuestion = ToolQuestion::findByShort('heat-source-warm-tap-water');
        $heatSourceWaterQuestionOther = ToolQuestion::findByShort('heat-source-warm-tap-water-other');
        $newHeatSourceQuestion = ToolQuestion::findByShort('new-heat-source');
        $newHeatSourceWaterQuestion = ToolQuestion::findByShort('new-heat-source-warm-tap-water');

        $this->infoLog("Deleting all (new)-heat-source-warm-tap-water answers except for none and sun-boiler");
        DB::table('tool_question_answers')
            ->where('tool_question_id', $heatSourceWaterQuestion->id)
            ->where('answer', '!=', 'sun-boiler')
            ->where('answer', '!=', 'none')
            ->delete();

        DB::table('tool_question_answers')
            ->where('tool_question_id', $newHeatSourceWaterQuestion->id)
            ->where('answer', '!=', 'sun-boiler')
            ->delete();

        foreach ($relevantInputSources as $inputSource) {
            $buildingsQuery = DB::table('tool_question_answers')
                ->where('tool_question_id', $heatSourceQuestion->id)
                ->where('input_source_id', $inputSource->id)
                ->select('id', 'building_id', 'input_source_id')
                ->distinct();

            $total = $buildingsQuery->count();
            $this->infoLog("Starting heat-source to single heat-source-warm-tap-water map for input source {$inputSource->id} for a total of {$total} answers");

            $i = 0;

            $buildingsQuery->orderBy('id')->chunkById(100, function ($buildings) use (&$buildingsToRecalculate, &$i, $total, $heatSourceQuestion, $heatSourceWaterQuestion, $heatSourceQuestionOther, $heatSourceWaterQuestionOther) {
                // Map relevant answers from heat-source to heat-source-warm-tap-water
                // $building is a ToolQuestionCustomValue
                foreach ($buildings as $building) {
                    $hasNone = DB::table('tool_question_answers')
                        ->where('tool_question_id', $heatSourceWaterQuestion->id)
                        ->where('input_source_id', $building->input_source_id)
                        ->where('building_id', $building->building_id)
                        ->where('answer', 'none')
                        ->first() instanceof \stdClass;

                    $answersForBuilding = DB::table('tool_question_answers')
                        ->where('tool_question_id', $heatSourceQuestion->id)
                        ->where('input_source_id', $building->input_source_id)
                        ->where('building_id', $building->building_id)
                        ->pluck('answer')
                        ->toArray();

                    $newAnswerForBuilding = [];

                    if (in_array('hr-boiler', $answersForBuilding)) {
                        $newAnswerForBuilding = ['hr-boiler'];
                    } elseif (in_array('heat-pump', $answersForBuilding)) {
                        $newAnswerForBuilding = ['heat-pump'];
                    } elseif (in_array('district-heating', $answersForBuilding)) {
                        $newAnswerForBuilding = ['district-heating'];
                    } elseif (in_array('infrared', $answersForBuilding)) {
                        $newAnswerForBuilding = ['electric-boiler'];
                    } elseif (in_array('none', $answersForBuilding)) {
                        $newAnswerForBuilding = ['none'];

                        if (! $hasNone) {
                            // User has no different answer, so we set the one from heat source

                            $otherAnswer =  DB::table('tool_question_answers')
                                ->where("building_id", $building->building_id)
                                ->where('input_source_id', $building->input_source_id)
                                ->where('tool_question_id', $heatSourceQuestionOther->id)
                                ->first()->answer ?? null;

                            DB::table('tool_question_answers')
                                ->updateOrInsert([
                                    'building_id' => $building->building_id,
                                    'input_source_id' => $building->input_source_id,
                                    'tool_question_id' => $heatSourceWaterQuestionOther->id,
                                ], ['answer' => $otherAnswer, 'created_at' => now(), 'updated_at' => now()]);
                        }
                    }

                    // Always add sun-boiler as extra
                    if (in_array('sun-boiler', $answersForBuilding)) {
                        $newAnswerForBuilding[] = 'sun-boiler';
                    }

                    // Ensure we delete this if it's not in the new array
                    if ($hasNone && ! in_array('none', $newAnswerForBuilding)) {
                        DB::table('tool_question_answers')
                            ->where('tool_question_id', $heatSourceWaterQuestion->id)
                            ->where("building_id", $building->building_id)
                            ->where('input_source_id', $building->input_source_id)
                            ->where('answer', '=', 'none')
                            ->delete();
                    }

                    foreach ($newAnswerForBuilding as $newAnswer) {
                        $customValueId = ToolQuestionCustomValue::where('tool_question_id', $heatSourceWaterQuestion->id)
                            ->whereShort($newAnswer)
                            ->first()->id;

                        DB::table('tool_question_answers')
                            ->updateOrInsert([
                                'building_id' => $building->building_id,
                                'input_source_id' => $building->input_source_id,
                                'tool_question_id' => $heatSourceWaterQuestion->id,
                                'tool_question_custom_value_id' => $customValueId,
                            ], ['answer' => $newAnswer, 'created_at' => now(), 'updated_at' => now()]);
                    }

                    $buildingsToRecalculate[] = $building->building_id;
                    ++$i;

                    if ($i % 1000 === 0) {
                        $this->infoLog("{$i} / {$total}");
                    }
                }
            });

            $buildingsQuery = DB::table('tool_question_answers')
                ->where('tool_question_id', $newHeatSourceQuestion->id)
                ->where('input_source_id', $inputSource->id)
                ->select('id', 'building_id', 'input_source_id')
                ->distinct();

            $total = $buildingsQuery->count();
            $this->infoLog("Starting new-heat-source to single new-heat-source-warm-tap-water map for input source {$inputSource->id} for a total of {$total} answers");

            $i = 0;

            $buildingsQuery->orderBy('id')->chunkById(100, function ($buildings) use (&$buildingsToRecalculate, &$i, $total, $newHeatSourceQuestion, $newHeatSourceWaterQuestion) {
                // Map relevant answers from new-heat-source to new-heat-source-warm-tap-water
                // $building is a ToolQuestionCustomValue
                foreach ($buildings as $building) {
                    $answersForBuilding = DB::table('tool_question_answers')
                        ->where('tool_question_id', $newHeatSourceQuestion->id)
                        ->where('input_source_id', $building->input_source_id)
                        ->where('building_id', $building->building_id)
                        ->pluck('answer')
                        ->toArray();

                    $newAnswerForBuilding = [];

                    if (in_array('hr-boiler', $answersForBuilding)) {
                        $newAnswerForBuilding = ['hr-boiler'];
                    } elseif (in_array('heat-pump', $answersForBuilding)) {
                        $newAnswerForBuilding = ['heat-pump'];
                    } elseif (in_array('district-heating', $answersForBuilding)) {
                        $newAnswerForBuilding = ['district-heating'];
                    } elseif (in_array('infrared', $answersForBuilding)) {
                        $newAnswerForBuilding = ['electric-boiler'];
                    }

                    // Always add sun-boiler as extra
                    if (in_array('sun-boiler', $answersForBuilding)) {
                        $newAnswerForBuilding[] = 'sun-boiler';
                    }

                    foreach ($newAnswerForBuilding as $newAnswer) {
                        $customValueId = ToolQuestionCustomValue::where('tool_question_id', $newHeatSourceWaterQuestion->id)
                            ->whereShort($newAnswer)
                            ->first()->id;

                        DB::table('tool_question_answers')
                            ->updateOrInsert([
                                'building_id' => $building->building_id,
                                'input_source_id' => $building->input_source_id,
                                'tool_question_id' => $newHeatSourceWaterQuestion->id,
                                'tool_question_custom_value_id' => $customValueId,
                            ], ['answer' => $newAnswer, 'created_at' => now(), 'updated_at' => now()]);
                    }

                    $buildingsToRecalculate[] = $building->building_id;
                    ++$i;

                    if ($i % 1000 === 0) {
                        $this->infoLog("{$i} / {$total}");
                    }
                }
            });
        }
    }

    private function infoLog($info)
    {
        $this->info($info);
        Log::debug($info);
    }
}