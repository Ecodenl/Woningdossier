<?php

namespace App\Console\Commands\Fixes;

use Illuminate\Console\Command;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class CorrectHasSolarPanelsToolQuestionAnswer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixes:correct-has-solar-panels-tool-question-answer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'If someone has a positive count on the solar-panel-count tool question, we will set the has-solar-panels question to yes';

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

        $toolQuestionAnswersToUpdate = DB::table('building_services')
            ->select(['tool_question_answers.id', 'building_services.building_id', 'building_services.input_source_id'])
            ->leftJoin('tool_question_answers', function(JoinClause $join) {
                $join
                    ->on('building_services.building_id', '=', 'tool_question_answers.building_id')
                    ->whereRaw('building_services.input_source_id = tool_question_answers.input_source_id');
            })
            ->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(building_services.extra, "$.value")) > 1')
            ->whereRaw('tool_question_answers.tool_question_id = (select id from tool_questions where short = "has-solar-panels")')
            ->where('answer', 'no')
            ->groupBy(['tool_question_answers.id', 'building_services.building_id', 'building_services.input_source_id'])
            ->get();

        $updatedCount = DB::table('tool_question_answers')
            ->whereIn('id', $toolQuestionAnswersToUpdate->pluck('id'))
            ->update(['answer' => 'yes', 'tool_question_custom_value_id' => 30]);

        $this->info("Total tool question answers corrected: {$updatedCount}");
    }
    
    
    
}
