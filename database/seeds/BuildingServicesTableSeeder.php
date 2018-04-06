<?php

use Illuminate\Database\Seeder;

class BuildingServicesTableSeeder extends Seeder
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
                        'values' => [
                            'nl' => 'Met normale radiatoren',
                        ],
                        'calculate_value' => 1,
                    ],
                    [
                        'values' => [
                            'nl' =>' Met normale radiatoren en vloerverwarming',
                        ],
                        'calculate_value' => 2,
                    ],
                    [
                        'values' => [
                            'nl' => 'Alleen met vloerverwarming',
                        ],
                        'calculate_value' => 3,
                    ],
                    [
                        'values' => [
                            'nl' => 'Met lage temperatuur radiatoren en vloerverwarming',
                        ],
                        'calculate_value' => 4,
                    ],
                    [
                        'values' => [
                            'nl' => 'Met lage temperatuur radiatoren',
                        ],
                        'calculate_value' => 5,
                    ],
                ],
            ],
        ];

        // todo: fix
//        foreach ($heatings as $heating) {
//            $uuid = App\Helpers\Str::uuid();
//            foreach ($heating['names'] as $locale => $name) {
//                \App\Models\Translation::create([
//                    'key' => $uuid,
//                    'language' => $locale,
//                    'translation' => $name
//                ]);
//            }
//
//
//            $nameUuid = \DB::table('translations')
//                ->where('translation', $heating['service_type'])
//                ->where('language', 'en')
//                ->first(['key']);
//
//            // Get the category. If it doesn't exist: create it
//            $serviceType = \DB::table('service_types')->where('name', $nameUuid->key)->first();
//
//            if ($serviceType instanceof stdClass) {
//                $buildingServiceId = \DB::table( 'building_services' )->insertGetId( [
//                    'name'            => $uuid,
//                    'service_type_id' => $serviceType->id,
//                ]);
//
//                foreach ($heatings['service_values'] as $heatingValue)
//                    \DB::table('building_service_values')->insert([
//                        'building_service_id' => $buildingServiceId,
//                        'value' => $uuid,
//                        'calculate_value' => isset($heatingValue['calculate_value']) ? $heatingValue['calculate_value'] : null,
//                    ]);
//                }
//
//        }
    }
}
