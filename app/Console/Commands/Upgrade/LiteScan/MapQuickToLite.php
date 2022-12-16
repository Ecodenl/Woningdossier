<?php

namespace App\Console\Commands\Upgrade\LiteScan;

use App\Models\InputSource;
use App\Models\Step;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MapQuickToLite extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:lite-scan:map-quick-to-lite';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update cooperation measures.';

    public function handle()
    {
        $inputSources = InputSource::all();

        $steps = [
            'building-data' => 'building-data-lite',
            'usage-quick-scan' => 'usage-lite-scan',
            'living-requirements' => 'living-requirements-lite',
            'residential-status' => 'residential-status-lite',
        ];

        // So technically this is an exception to the rule, as it has non-matching conditions. However, if we
        // complete it, and it's not visible, it won't have impact on the tool, and the conditionals will incomplete
        // it when a relevant question is changed. So, we won't have to make this mapping more complex.
        //$exception = '50-graden-test';

        $subStepMap = [];
        foreach ($steps as $quickStepShort => $liteStepShort) {
            $quickStep = Step::findByShort($quickStepShort);
            $liteStep = Step::findByShort($liteStepShort);

            $subStepMap = array_merge($subStepMap,
                DB::table('sub_steps AS a')
                    ->select('a.id AS from', 'b.id AS to')
                    ->leftJoin('sub_steps AS b', 'a.slug->nl', '=', 'b.slug->nl')
                    ->where('a.step_id', $quickStep->id)
                    ->where('b.step_id', $liteStep->id)
                    ->get()
                    ->toArray()
            );

            // We can 1 to 1 map steps to be completed if it's not living requirements. Living requirements
            // has a non-matching sub step.
            if ($quickStepShort !== 'living-requirements') {
                foreach ($inputSources as $inputSource) {
                    Log::debug("Processing step {$quickStepShort} to {$liteStepShort} for {$inputSource->short}");
                    $buildings = DB::table('completed_steps')
                        ->leftJoin('buildings', 'completed_steps.building_id', '=', 'buildings.id')
                        ->where("input_source_id", $inputSource->id)
                        ->where("step_id", $quickStep->id)
                        ->whereNull('buildings.deleted_at')
                        ->whereNotNull('buildings.user_id')
                        ->pluck('completed_steps.building_id')
                        ->toArray();

                    $data = [];
                    $base = [
                        'input_source_id' => $inputSource->id,
                        'step_id' => $liteStep->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    foreach ($buildings as $buildingId) {
                        $copy = $base;
                        $copy['building_id'] = $buildingId;
                        $data[] = $copy;
                    }

                    // Use chunk, because else the insert might become too large for SQL to handle
                    foreach (array_chunk($data, 5000) as $dataToInsert) {
                        DB::table('completed_steps')->insert($dataToInsert);
                    }
                }
            }
        }

        foreach ($inputSources as $inputSource) {
            foreach ($subStepMap as $mapping) {
                Log::debug("Processing sub step {$mapping->from} to {$mapping->to} for {$inputSource->short}");
                $buildings = DB::table('completed_sub_steps')
                    ->leftJoin('buildings', 'completed_sub_steps.building_id', '=', 'buildings.id')
                    ->where("input_source_id", $inputSource->id)
                    ->where("sub_step_id", $mapping->from)
                    ->whereNull('buildings.deleted_at')
                    ->whereNotNull('buildings.user_id')
                    ->pluck('completed_sub_steps.building_id')
                    ->toArray();

                $data = [];
                $base = [
                    'input_source_id' => $inputSource->id,
                    'sub_step_id' => $mapping->to,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                foreach ($buildings as $buildingId) {
                    $copy = $base;
                    $copy['building_id'] = $buildingId;
                    $data[] = $copy;
                }

                // Use chunk, because else the insert might become too large for SQL to handle
                foreach (array_chunk($data, 5000) as $dataToInsert) {
                    DB::table('completed_sub_steps')->insert($dataToInsert);
                }
            }
        }
    }
}