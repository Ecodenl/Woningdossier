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
        $heatSourceWaterQuestion = ToolQuestion::findByShort('heat-source-warm-tap-water');
        $heatSourceWaterQuestionOther = ToolQuestion::findByShort('heat-source-warm-tap-water-other');

        // TODO: How will we map sun boiler?
        // TODO: How will we map "other"?
        $this->infoLog('Deleting all heat-source-warm-tap-water answers');
        DB::table('tool_question_answers')
            ->where('tool_question_id', $heatSourceWaterQuestion->id)
            //->where('answer', '!=', 'sun-boiler')
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

            $buildingsQuery->orderBy('id')->chunkById(100, function ($buildings) use (&$buildingsToRecalculate, &$i, $total, $heatSourceQuestion, $heatSourceWaterQuestion) {
                // Map relevant answers from heat-source to heat-source-warm-tap-water
                // $building is a ToolQuestionCustomValue
                foreach ($buildings as $building) {
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
                    }

                    // TODO: Sun boiler?
                    //if (in_array('sun-boiler', $answersForBuilding)) {
                    //    $newAnswerForBuilding[] = 'sun-boiler';
                    //}

                    // TODO: Other?
                    foreach ($newAnswerForBuilding as $newAnswer) {
                        $customValueId = ToolQuestionCustomValue::where('tool_question_id', $heatSourceWaterQuestion->id)
                            ->whereShort($newAnswer)
                            ->first()->id;

                        DB::table('tool_question_answers')
                            ->insert([
                                'building_id' => $building->building_id,
                                'input_source_id' => $building->input_source_id,
                                'tool_question_id' => $heatSourceWaterQuestion->id,
                                'tool_question_custom_value_id' => $customValueId,
                                'answer' => $newAnswer
                            ]);
                    }

                    $buildingsToRecalculate[] = $building->building_id;
                    ++$i;

                    if ($i % 1000 === 0) {
                        $this->infoLog("{$i} / {$total}");
                    }
                }
            });
        }

        // TODO: Copy paste above but for new
    }

    private function infoLog($info)
    {
        $this->info($info);
        Log::debug($info);
    }
}