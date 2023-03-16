<?php

namespace App\Console\Commands\Deploys;

use App\Models\CooperationMeasureApplication;
use App\Models\CustomMeasureApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixMeasureCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploys:fix-measure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix measure on prod';

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
        $deletedMeasures = [
            270 => 'Kozijnen',
            452 => 'Kozijnen',
            1203 => 'Hybride warmtepomp'
        ];

        foreach ($deletedMeasures as $id => $name) {
            $groupedAdvices = DB::table('user_action_plan_advices')
                ->where('user_action_plan_advisable_type', CooperationMeasureApplication::class)
                ->where('user_action_plan_advisable_id', $id)
                ->get()
                ->groupBy('user_id');

            foreach ($groupedAdvices as $userId => $advices) {
                $building = DB::table('buildings')->where('user_id', $userId)->first();

                foreach ($advices as $advice) {
                    // We don't need to worry about the measure not existing. While the advices didn't get updated
                    // correctly, the measures were made for the right input sources.
                    $customMeasure = DB::table('custom_measure_applications')
                        ->where('name->nl', $name)
                        ->where('building_id', $building->id)
                        ->where('input_source_id', $advice->input_source_id)
                        ->first();

                    // Should always be the case, but just to be sure
                    if ($customMeasure instanceof \stdClass) {
                        $measureExists = DB::table('user_action_plan_advices')
                            ->where('user_id', $userId)
                            ->where('user_action_plan_advisable_type', CustomMeasureApplication::class)
                            ->where('user_action_plan_advisable_id', $customMeasure->id)
                            ->where('input_source_id', $customMeasure->input_source_id)
                            ->exists();

                        if ($measureExists) {
                            Log::debug('Deleting advice with ID ' . $advice->id . ' from user ' . $userId . ' for deleted cooperation measure ' . $id);

                            // Already copied over, so we can safely delete this one.
                            DB::table('user_action_plan_advices')->where('id', $advice->id)->delete();
                        } else {
                            Log::debug('Converting advice with ID ' . $advice->id . ' from user ' . $userId . ' for deleted cooperation measure ' . $id . ' to custom measure with ID ' . $customMeasure->id);

                            // Not copied over yet. We must convert this advice to become the custom measure.
                            DB::table('user_action_plan_advices')
                                ->where('id', $advice->id)
                                ->update([
                                    'user_action_plan_advisable_type' => CustomMeasureApplication::class,
                                    'user_action_plan_advisable_id' => $customMeasure->id,
                                ]);
                        }
                    } else {
                        Log::debug('No custom measure found for advice with ID ' . $advice->id . ' from user ' . $userId . ' for deleted cooperation measure ' . $id . ' and input source ' . $advice->input_source_id);
                    }
                }
            }
        }
    }
}
