<?php

namespace App\Console\Commands\Upgrade\Merge;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\Str;
use App\Models\Building;
use App\Models\CompletedSubStep;
use App\Models\Cooperation;
use App\Models\CooperationMeasureApplication;
use App\Models\InputSource;
use App\Models\RoofType;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FixCooperationMeasures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:fix-cooperation-measures';

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
        $cooperations = Cooperation::all();

        $data = [];
        foreach ($cooperations as $cooperation) {
            $userIds = DB::table('cooperations')
                ->select('users.id')
                ->leftJoin('users', 'users.cooperation_id', '=', 'cooperations.id')
                ->where('cooperations.slug', $cooperation->slug)
                ->pluck('id')->toArray();

            $cooperationMeasureIds = DB::table('cooperation_measure_applications')
                ->where('cooperation_id', $cooperation->id)
                ->pluck('id')->toArray();

            $data[$cooperation->slug] = DB::table('user_action_plan_advices')
                ->whereIn('user_id', $userIds)
                ->whereNotIn('user_action_plan_advisable_id', $cooperationMeasureIds)
                ->where('user_action_plan_advisable_Type', CooperationMeasureApplication::class)
                ->pluck('user_action_plan_advisable_id', 'id')->toArray();
        }
        dd($data); // TODO: Decide how to handle this

        return 0;
    }
}