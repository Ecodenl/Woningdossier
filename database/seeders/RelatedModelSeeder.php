<?php

namespace Database\Seeders;

use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\ToolQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RelatedModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stepSmallMeasures = Step::findByShort('small-measures');
        $allExpertEnergySavingMeasures = MeasureApplication::measureType(MeasureApplication::ENERGY_SAVING)
            ->where('step_id', '!=', $stepSmallMeasures->id)->get();

        $relatedModels = [
            //[
            //    'from_model_type' => ToolQuestion::class,
            //    'from_model_id' => '',
            //    'target_model_type' => MeasureApplication::class,
            //    'target_model_id' => '',
            //],
        ];

        foreach ($allExpertEnergySavingMeasures as $measure) {
            $relatedModels[] = [
                'from_model_type' => ToolQuestion::class,
                'from_model_id' => "user-costs-{$measure->short}-own-total",
                'target_model_type' => MeasureApplication::class,
                'target_model_id' => $measure->id,
            ];
            $relatedModels[] = [
                'from_model_type' => ToolQuestion::class,
                'from_model_id' => "user-costs-{$measure->short}-subsidy-total",
                'target_model_type' => MeasureApplication::class,
                'target_model_id' => $measure->id,
            ];
            $relatedModels[] = [
                'from_model_type' => ToolQuestion::class,
                'from_model_id' => "execute-{$measure->short}-how",
                'target_model_type' => MeasureApplication::class,
                'target_model_id' => $measure->id,
            ];
        }

        foreach ($relatedModels as $relatedModel) {
            // Current construct expects short models only. If that changes, this will require rework!
            if (is_string($relatedModel['from_model_id'])) {
                $relatedModel['from_model_id'] = DB::table((new $relatedModel['from_model_type'])->getTable())
                    ->where('short', $relatedModel['from_model_id'])
                    ->first()
                    ->id;
            }
            if (is_string($relatedModel['target_model_id'])) {
                $relatedModel['target_model_id'] = DB::table((new $relatedModel['target_model_type'])->getTable())
                    ->where('short', $relatedModel['target_model_id'])
                    ->first()
                    ->id;
            }

            DB::table('related_models')->updateOrInsert($relatedModel);
        }
    }
}
