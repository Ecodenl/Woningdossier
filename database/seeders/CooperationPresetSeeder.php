<?php

namespace Database\Seeders;

use App\Services\Models\CooperationPresetService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CooperationPresetSeeder extends Seeder
{
    public function run(): void
    {
        $presets = [
            [
                'title' => 'Cooperatie maatregelen',
                'short' => CooperationPresetService::COOPERATION_MEASURE_APPLICATIONS,
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