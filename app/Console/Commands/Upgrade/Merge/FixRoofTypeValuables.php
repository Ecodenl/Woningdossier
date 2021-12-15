<?php

namespace App\Console\Commands\Upgrade\Merge;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\Str;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\RoofType;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixRoofTypeValuables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:fix-roof-type-valuables';

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
        $atomicMaker = DB::table('tool_question_valuables')
            ->where('tool_question_valuable_type', RoofType::class)
            ->where('tool_question_valuable_id', 7)
            ->first();

        if ($atomicMaker instanceof \stdClass) {
            DB::statement("UPDATE tool_question_valuables
                SET tool_question_valuable_id = tool_question_valuable_id - 1
                WHERE tool_question_valuable_type = ?
                AND tool_question_valuable_id > 4", [RoofType::class]);
        }

        return 0;
    }
}