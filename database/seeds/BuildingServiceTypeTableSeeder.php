<?php

use Illuminate\Database\Seeder;

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
                'names' => [
                    'nl' => 'Hoe word de woning nu verwarmd'
                ],
                'service_type' => 'Heating',
                'info' => [
                    'nl' => 'Info hier'
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
            $uuid = App\Helpers\Str::uuid();
            foreach ($heating['names'] as $locale => $name) {
                \App\Models\Translation::create([
                    'key' => $uuid,
                    'language' => $locale,
                    'translation' => $name
                ]);
            }

            foreach ($heating['service_values'] as $heatingValue)
                \DB::table('building_service_values')->insert([
                    'name' => $uuid,
                    'calculate_value' => isset($heatingValue['calculate_value']) ? $heatingValue['calculate_value'] : null,
            ]);

        }
    }
}
