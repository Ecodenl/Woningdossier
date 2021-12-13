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
        // Which cooperation decided to add a measure
        $cooperationSlug = 'duec';
        // The id of the old measure
        $boggedMeasureId = 464;

        $cooperation = DB::table('cooperations')
            ->where('slug', $cooperationSlug)
            ->first();

        if($cooperation instanceof \stdClass) {
            // The measure data
            $cooperationMeasureApplicationData = [
                'name' => json_encode([
                    'nl' => 'Zonnepanelen',
                ]),
                'info' => json_encode([
                    'nl' => 'Zonnepanelen actie',
                ]),
                'costs' => json_encode([
                    'from' => 1000,
                    'to' => 2000,
                ]),
                'savings_money' => 300.00,
                'extra' => json_encode([
                    'icon' => 'icon-solar-panels',
                ]),
                'cooperation_id' => $cooperation->id,
                'created_at' => '2021-11-02 17:06:58',
                'updated_at' => '2021-11-02 17:06:58',
            ];

            $userIds = DB::table('cooperations')
                ->select('users.id')
                ->leftJoin('users', 'users.cooperation_id', '=', 'cooperations.id')
                ->where('cooperations.slug', $cooperationSlug)
                ->pluck('id')->toArray();

            $cooperationMeasureApplication = DB::table('cooperation_measure_applications')
                ->where('cooperation_id', $cooperation->id)
                ->where('name->nl', 'Zonnepanelen')
                ->first();

            if (! $cooperationMeasureApplication instanceof \stdClass) {
                $cooperationMeasureApplication = DB::table('cooperation_measure_applications')
                    ->insert($cooperationMeasureApplicationData);
            }

            // Do this to make it atomic. If we check these, even if the new ID equals the bogged ID, it will
            // be in this set, so it won't update anything
            $cooperationMeasureIds = DB::table('cooperation_measure_applications')
                ->where('cooperation_id', $cooperation->id)
                ->pluck('id')->toArray();

            DB::table('user_action_plan_advices')
                ->whereIn('user_id', $userIds)
                ->whereNotIn('user_action_plan_advisable_id', $cooperationMeasureIds)
                ->where('user_action_plan_advisable_id', $boggedMeasureId)
                ->where('user_action_plan_advisable_type', CooperationMeasureApplication::class)
                ->update([
                    'user_action_plan_advisable_id' => $cooperationMeasureApplication->id,
                ]);
        }

        return 0;
    }
}