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
        $relevantInputSources = DB::table('input_sources')->get();

        $heatSourceWaterQuestion = ToolQuestion::findByShort('heat-source-warm-tap-water');

        foreach ($relevantInputSources as $inputSource) {
            $buildings = DB::table('tool_question_answers')
                ->select('building_id')
                ->where('tool_question_id', $heatSourceWaterQuestion->id)
                ->where('input_source_id', $inputSource->id)
                ->groupBy('building_id')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            $total = $buildings->count();
            $this->infoLog("{$total} buildings have more than 1 answer for heat-source-warm-tap-water for source {$inputSource->short}");

            $i = 0;

            foreach ($buildings as $building) {
                $answers = DB::table('tool_question_answers')
                    ->where('tool_question_id', $heatSourceWaterQuestion->id)
                    ->where('building_id', $building->building_id)
                    ->where('input_source_id', $inputSource->id)
                    ->get();

                $idsToKeep = [];

                if ($answers->contains('answer', 'hr-boiler')) {
                    $idsToKeep[] = $answers->where('answer', 'hr-boiler')->first()->id;
                } elseif ($answers->contains('answer', 'heat-pump')) {
                    $idsToKeep[] = $answers->where('answer', 'heat-pump')->first()->id;
                } elseif ($answers->contains('answer', 'district-heating')) {
                    $idsToKeep[] = $answers->where('answer', 'district-heating')->first()->id;
                } elseif ($answers->contains('answer', 'electric-boiler')) {
                    $idsToKeep[] = $answers->where('answer', 'electric-boiler')->first()->id;
                } elseif ($answers->contains('answer', 'heat-pump-boiler')) {
                    $idsToKeep[] = $answers->where('answer', 'heat-pump-boiler')->first()->id;
                } elseif ($answers->contains('answer', 'kitchen-geyser')) {
                    $idsToKeep[] = $answers->where('answer', 'kitchen-geyser')->first()->id;
                } elseif ($answers->contains('answer', 'none')) {
                    $idsToKeep[] = $answers->where('answer', 'none')->first()->id;
                }

                // Always keep sun boiler
                if ($answers->contains('answer', 'sun-boiler')) {
                    $idsToKeep[] = $answers->where('answer', 'sun-boiler')->first()->id;
                }

                DB::table('tool_question_answers')
                    ->where('tool_question_id', $heatSourceWaterQuestion->id)
                    ->where('building_id', $building->building_id)
                    ->where('input_source_id', $inputSource->id)
                    ->whereNotIn('id', $idsToKeep)
                    ->delete();

                ++$i;

                if ($i % 1000 === 0) {
                    $this->infoLog("{$i} / {$total}");
                }
            }
        }

        $newHeatSourceWaterQuestion = ToolQuestion::findByShort('new-heat-source-warm-tap-water');

        foreach ($relevantInputSources as $inputSource) {
            $buildings = DB::table('tool_question_answers')
                ->select('building_id')
                ->where('tool_question_id', $newHeatSourceWaterQuestion->id)
                ->where('input_source_id', $inputSource->id)
                ->groupBy('building_id')
                ->havingRaw('COUNT(*) > 1')
                ->get();

            $total = $buildings->count();
            $this->infoLog("{$total} buildings have more than 1 answer for new-heat-source-warm-tap-water for source {$inputSource->short}");

            $i = 0;

            foreach ($buildings as $building) {
                $answers = DB::table('tool_question_answers')
                    ->where('tool_question_id', $newHeatSourceWaterQuestion->id)
                    ->where('building_id', $building->building_id)
                    ->where('input_source_id', $inputSource->id)
                    ->get();

                $idsToKeep = [];

                if ($answers->contains('answer', 'hr-boiler')) {
                    $idsToKeep[] = $answers->where('answer', 'hr-boiler')->first()->id;
                } elseif ($answers->contains('answer', 'heat-pump')) {
                    $idsToKeep[] = $answers->where('answer', 'heat-pump')->first()->id;
                } elseif ($answers->contains('answer', 'district-heating')) {
                    $idsToKeep[] = $answers->where('answer', 'district-heating')->first()->id;
                } elseif ($answers->contains('answer', 'electric-boiler')) {
                    $idsToKeep[] = $answers->where('answer', 'electric-boiler')->first()->id;
                } elseif ($answers->contains('answer', 'heat-pump-boiler')) {
                    $idsToKeep[] = $answers->where('answer', 'heat-pump-boiler')->first()->id;
                } elseif ($answers->contains('answer', 'kitchen-geyser')) {
                    $idsToKeep[] = $answers->where('answer', 'kitchen-geyser')->first()->id;
                } elseif ($answers->contains('answer', 'none')) {
                    $idsToKeep[] = $answers->where('answer', 'none')->first()->id;
                }

                // Always keep sun boiler
                if ($answers->contains('answer', 'sun-boiler')) {
                    $idsToKeep[] = $answers->where('answer', 'sun-boiler')->first()->id;
                }

                DB::table('tool_question_answers')
                    ->where('tool_question_id', $newHeatSourceWaterQuestion->id)
                    ->where('building_id', $building->building_id)
                    ->where('input_source_id', $inputSource->id)
                    ->whereNotIn('id', $idsToKeep)
                    ->delete();

                ++$i;

                if ($i % 1000 === 0) {
                    $this->infoLog("{$i} / {$total}");
                }
            }
        }
    }

    private function infoLog($info)
    {
        $this->info($info);
        Log::debug($info);
    }
}