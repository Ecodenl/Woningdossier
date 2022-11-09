<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingServiceTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $heatings = [
            [
                'name' => [
                    'nl' => 'Hoe word de woning nu verwarmd',
                ],
                'service_type' => 'Heating',
                'info' => [
                    'nl' => 'Info hier',
                ],
                'service_values' => [
                    [
                        'name' => [
                            'nl' => 'Met normale radiatoren',
                        ],
                        'calculate_value' => 1,
                    ],
                    [
                        'name' => [
                            'nl' =>' Met normale radiatoren en vloerverwarming',
                        ],
                        'calculate_value' => 2,
                    ],
                    [
                        'name' => [
                            'nl' => 'Alleen met vloerverwarming',
                        ],
                        'calculate_value' => 3,
                    ],
                    [
                        'name' => [
                            'nl' => 'Met lage temperatuur radiatoren en vloerverwarming',
                        ],
                        'calculate_value' => 4,
                    ],
                    [
                        'name' => [
                            'nl' => 'Met lage temperatuur radiatoren',
                        ],
                        'calculate_value' => 5,
                    ],
                ],
            ],
        ];

        // todo: fix
        foreach ($heatings as $heating) {
            foreach ($heating['service_values'] as $heatingValue) {
                DB::table('building_service_values')->insert([
                    'name' => json_encode($heatingValue['name']),
                    'calculate_value' => isset($heatingValue['calculate_value']) ? $heatingValue['calculate_value'] : null,
            ]);
            }
        }
    }
}
