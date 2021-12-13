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

class FixSpecificExampleBuilding extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:fix-specific-example-building';

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
        $subStep = DB::table('sub_steps')->where('slug->nl', 'specifieke-voorbeeld-woning')->first();

        $buildingIds = DB::table('building_features')
            ->distinct()
            ->whereNotNull('example_building_id')
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

        return 0;
    }
}