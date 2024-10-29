<?php

namespace Database\Seeders;

use App\Services\Models\CooperationPresetService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CooperationPresetContentSeeder extends Seeder
{
    public function run(): void
    {
        $contentByShort = [
            CooperationPresetService::COOPERATION_MEASURE_APPLICATIONS => CooperationMeasureApplicationsTableSeeder::MEASURES,
        ];

        foreach ($contentByShort as $short => $contents) {
            $preset = DB::table('cooperation_presets')->where('short', $short)->first();

            if ($preset instanceof \stdClass) {
                // Only seed if there's no content yet, as we can't really query on JSON
                if (DB::table('cooperation_preset_contents')->where('cooperation_preset_id', $preset->id)->doesntExist()) {
                    foreach ($contents as $content) {
                        DB::table('cooperation_preset_contents')->insert(
                            [
                                'cooperation_preset_id' => $preset->id,
                                'content' => json_encode($content),
                            ]
                        );
                    }
                }
            }
        }
    }
}