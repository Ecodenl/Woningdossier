<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CooperationPresetContentSeeder extends Seeder
{
    public function run()
    {
        $contentByShort = [
            'cooperation-measure-applications' => CooperationMeasureApplicationsTableSeeder::MEASURES,
        ];

        foreach ($contentByShort as $short => $contents) {
            $preset = DB::table('cooperation_presets')->where('short', $short)->first();

            if ($preset instanceof \stdClass) {
                foreach ($contents as $content) {
                    // Not meant for update
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