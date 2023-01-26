<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CooperationPresetSeeder extends Seeder
{
    public function run()
    {
        $presets = [
            [
                'title' => 'Cooperatie maatregelen',
                'short' => 'cooperation-measure-applications',
            ],
        ];

        foreach ($presets as $preset) {
            DB::table('cooperation_presets')->updateOrInsert(
                ['short' => $preset['short']],
                $preset,
            );
        }
    }
}