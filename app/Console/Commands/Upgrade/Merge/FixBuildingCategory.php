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

class FixBuildingCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:fix-building-category';

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
        $subStep = DB::table('sub_steps')->where('slug->nl', 'woning-type')->first();

        $buildingIds = DB::table('building_features')
            ->distinct()
            ->whereNotNull('building_type_id')
            ->pluck('building_id')->toArray();

        $setSubStepBuildingIds = DB::table('completed_sub_steps')
            ->distinct()
            ->where('sub_step_id', $subStep->id)
            ->whereIn('building_id', $buildingIds)
            ->pluck('building_id')->toArray();

        // All building ids missing the first sub step as completed
        $diffIds = array_diff($buildingIds, $setSubStepBuildingIds);

        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        $default = [
            'sub_step_id' => $subStep->id,
            'input_source_id' => $masterInputSource->id,
            'created_at' => now(),
        ];

        $data = [];

        foreach ($diffIds as $diffId) {
            $temp = $default;
            $temp['building_id'] = $diffId;
            $data[] = $temp;
        }

        // Insert missing data into DB
        DB::table('completed_sub_steps')->insert($data);

        // Cool fact: Sub step could not be completed, but the answer could be set...
        $toolQuestion = DB::table('tool_questions')
            ->where('short', 'building-type-category')
            ->first();

        $setAnswers = DB::table('tool_question_answers')
            ->distinct()
            ->where('tool_question_id', $toolQuestion->id)
            ->whereIn('building_id', $diffIds)
            ->where('input_source_id', $masterInputSource->id)
            ->pluck('building_id')->toArray();

        // Grab all building ids we need to answer for
        $diffIds = array_diff($diffIds, $setAnswers);

        $mapping = DB::table('building_types')
            ->pluck('building_type_category_id', 'id')->toArray();

        // Time to set the answers
        foreach ($diffIds as $diffId) {
            $base = DB::table('building_features')
                ->where('building_id', $diffId)
                ->whereNotNull('building_type_id');

            $answer = $base->where('input_source_id', $masterInputSource->id)->first();
            if (! $answer instanceof \stdClass) {
                // In my tests there were 6 buildings without a master answer. We just fix this as good
                // as we can...
                $answer = $base->first();
            }

            if ($answer instanceof \stdClass) {
                $category = $mapping[$answer->building_type_id];

                DB::table('tool_question_answers')->insert([
                    'building_id' => $diffId,
                    'input_source_id' => $masterInputSource->id,
                    'tool_question_id' => $toolQuestion->id,
                    'answer' => $category,
                    'created_at' => now(),
                ]);
            }
        }

        return 0;
    }
}