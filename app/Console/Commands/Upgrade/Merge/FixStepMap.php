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

class FixStepMap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:fix-step-map';

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
        $stepMap = [
            'building-characteristics' => 'building-data',
            'current-state' => 'residential-status',
            'usage' => 'usage-quick-scan',
            'interest' => 'living-requirements',
        ];

        foreach ($stepMap as $fromStepShort => $toStepShort) {
            $fromStep = DB::table('steps')->where('short', '=', $fromStepShort)->first();
            $toStep = DB::table('steps')->where('short', '=', $toStepShort)->first();

            // Map completed steps to new steps
            DB::table('completed_steps')->where('step_id', '=', $fromStep->id)
                ->update([
                    'step_id' => $toStep->id,
                    'updated_at' => now(),
                ]);
        }

        return 0;
    }
}