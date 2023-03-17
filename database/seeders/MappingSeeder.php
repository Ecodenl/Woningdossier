<?php

namespace Database\Seeders;

use App\Helpers\MappingHelper;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\ToolQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MappingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stepSmallMeasures = Step::findByShort('small-measures');
        $allExpertEnergySavingMeasures = MeasureApplication::measureType(MeasureApplication::ENERGY_SAVING)
            ->where('step_id', '!=', $stepSmallMeasures->id)->get();

        $mappings = [
            //[
            //    'type' => MappingHelper::TYPE_RELATED_MODEL,
            //    'conditions' => null,
            //    'from_model_type' => ToolQuestion::class,
            //    'from_model_id' => '',
            //    'from_value' => null,
            //    'target_model_type' => MeasureApplication::class,
            //    'target_model_id' => '',
            //    'target_value' => null,
            //    'target_data' => null,
            //],
        ];

        foreach ($allExpertEnergySavingMeasures as $measure) {
            $mappings[] = [
                'type' => MappingHelper::TYPE_RELATED_MODEL,
                'from_model_type' => ToolQuestion::class,
                'from_model_id' => "user-costs-{$measure->short}-own-total",
                'target_model_type' => MeasureApplication::class,
                'target_model_id' => $measure->id,
            ];
            $mappings[] = [
                'type' => MappingHelper::TYPE_RELATED_MODEL,
                'from_model_type' => ToolQuestion::class,
                'from_model_id' => "user-costs-{$measure->short}-subsidy-total",
                'target_model_type' => MeasureApplication::class,
                'target_model_id' => $measure->id,
            ];
            $mappings[] = [
                'type' => MappingHelper::TYPE_RELATED_MODEL,
                'from_model_type' => ToolQuestion::class,
                'from_model_id' => "execute-{$measure->short}-how",
                'target_model_type' => MeasureApplication::class,
                'target_model_id' => $measure->id,
            ];
        }

        foreach ($mappings as $mapping) {
            // Current construct expects short models only. If that changes, this will require rework
            if (is_string($mapping['from_model_id'])) {
                $mapping['from_model_id'] = DB::table((new $mapping['from_model_type'])->getTable())
                    ->where('short', $mapping['from_model_id'])
                    ->first()
                    ->id;
            }
            if (is_string($mapping['target_model_id'])) {
                $mapping['target_model_id'] = DB::table((new $mapping['target_model_type'])->getTable())
                    ->where('short', $mapping['target_model_id'])
                    ->first()
                    ->id;
            }

            // NOTE: Might not work with arrays. (e.g. target_data). Currently not relevant.
            DB::table('mappings')->updateOrInsert($mapping);
        }
    }
}
