<?php

namespace App\Console\Commands\Upgrade\Subsidy;

use App\Models\InputSource;
use App\Models\ToolQuestion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixContractType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:subsidy:fix-contract-type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix contract type for users who only have it for master.';

    public function handle()
    {
        $toolQuestion = DB::table('tool_questions')
            ->where('short', 'building-contract-type')
            ->first();

        $subStepIds = DB::table('sub_steppables')
            ->where('sub_steppable_type', ToolQuestion::class)
            ->where('sub_steppable_id', $toolQuestion->id)
            ->pluck('sub_step_id')
            ->toArray();

        $masterInputSource = DB::table('input_sources')
            ->where('short', InputSource::MASTER_SHORT)
            ->first();

        $buildings = DB::table('completed_sub_steps')
            ->select('building_id', 'input_source_id')
            ->whereIn('sub_step_id', $subStepIds)
            ->whereIn('building_id', function ($query) use ($toolQuestion, $masterInputSource) {
                $query->from('tool_question_answers')->select('building_id')
                    ->where('tool_question_id', $toolQuestion->id)
                    ->where('input_source_id', $masterInputSource->id)
                    ->whereIn('building_id', function ($query) use ($toolQuestion) {
                        $query->from('tool_question_answers')->select('building_id')
                            ->where('tool_question_id', $toolQuestion->id)
                            ->groupBy('building_id')
                            ->havingRaw('COUNT(*) = 1');
                    });
            })
            ->where('input_source_id', '!=', $masterInputSource->id)
            ->groupBy('building_id', 'input_source_id')
            ->orderBy('building_id')
            ->get();

        foreach ($buildings as $building) {
            $masterAnswer = DB::table('tool_question_answers')
                ->where('tool_question_id', $toolQuestion->id)
                ->where('input_source_id', $masterInputSource->id)
                ->where('building_id', $building->building_id)
                ->first();

            $dupe = (array) $masterAnswer;
            unset($dupe['id']);

            $dupe['input_source_id'] = $building->input_source_id;
            DB::table('tool_question_answers')->insert($dupe);
        }
    }
}
