<?php

use Illuminate\Database\Seeder;

class ServiceValuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = [
            [
                [
                    'names' => [
                        'nl' => 'Warmtepomp',
                    ],
                    'short' => 'heat-pump',
                    'service_type' => 'Heating',
                    'order' => 0,
                    'info' => [
                        'nl' => 'Hier kunt u aangeven of u in de huidige situatie in plaats van een cv-ketel een warmtepomp als enige warmteopwekker in huis hebt. U hebt de keuze uit een warmtepomp met buitenlucht of bodemenergie als warmtebron.',
                    ],
                    'service_values' => [
                        [
                            'values' => [
                                'nl' => 'Geen',
                            ],
                            'order' => 1,
                            'calculate_value' => 1,
                        ],
                        [
                            'values' => [
                                'nl' => 'Volledige warmtepomp buitenlucht',
                            ],
                            'order' => 2,
                            'calculate_value' => 2,
                        ],
                        [
                            'values' => [
                                'nl' => 'Volledige warmtepomp bodem',
                            ],
                            'order' => 3,
                            'calculate_value' => 3,
                        ],
                        [
                            'values' => [
                                'nl' => 'Hybride warmtepomp',
                            ],
                            'order' => 4,
                            'calculate_value' => 4,
                        ],
                        [
                            'values' => [
                                'nl' => 'Collectieve warmtepomp',
                            ],
                            'order' => 5,
                            'calculate_value' => 5,
                        ],
                    ],
                ],
                'names' => [
                    'nl' => 'Zonneboiler',
                ],
                'short' => 'sun-boiler',
                'service_type' => 'Heating',
                'order' => 0,
                'info' => [
                    'nl' => 'Infotext hier',
                ],
                'service_values' => [
                    [
                        'values' => [
                            'nl' => 'Geen',
                        ],
                        'order' => 1,
                        'calculate_value' => 1,
                    ],
                    [
                        'values' => [
                            'nl' => 'Voor warm tapwater',
                        ],
                        'order' => 2,
                        'calculate_value' => 2,
                    ],
                    [
                        'values' => [
                            'nl' => 'Voor verwarming',
                        ],
                        'order' => 3,
                        'calculate_value' => 3,
                    ],
                    [
                        'values' => [
                            'nl' => 'Voor verwarming en warm tapwater',
                        ],
                        'order' => 4,
                        'calculate_value' => 4,
                    ],
                ],
            ],
            [
                'names' => [
                    'nl' => 'HR CV ketel',
                ],
                'short' => 'hr-boiler',
                'service_type' => 'Heating',
                'order' => 0,
                'info' => [
                    'nl' => 'Info hier.',
                ],
                'service_values' => [
                    [
                        'values' => [
                            'nl' => 'Aanwezig, recent vervangen',
                        ],
                        'order' => 1,
                        'calculate_value' => 1,
                    ],
                    [
                        'values' => [
                            'nl' => 'Aanwezig, tussen 6 en 13 jaar oud',
                        ],
                        'order' => 2,
                        'calculate_value' => 1,
                    ],
                    [
                        'values' => [
                            'nl' => 'Aanwezig, ouder dan 13 jaar',
                        ],
                        'order' => 3,
                        'calculate_value' => 2,
                    ],
                    [
                        'values' => [
                            'nl' => 'Niet aanwezig',
                        ],
                        'order' => 4,
                        'calculate_value' => 2,
                    ],
                    [
                        'values' => [
                            'nl' => 'Onbekend',
                        ],
                        'order' => 2,
                        'calculate_value' => 5,
                    ],
                ],
            ],
            [
                'names' => [
                    'nl' => 'Type ketel',
                ],
                'short' => 'boiler',
                'service_type' => 'Heating',
                'order' => 0,
                'info' => [
                    'nl' => 'Info hier.',
                ],
                'service_values' => [
                    [
                        'values' => [
                            'nl' => 'conventioneel rendement ketel',
                        ],
                        'order' => 1,
                        'calculate_value' => 1,
                        'is_default' => false,
                    ],
                    [
                        'values' => [
                            'nl' => 'verbeterd rendement ketel',
                        ],
                        'order' => 2,
                        'calculate_value' => 2,
                        'is_default' => false,
                    ],
                    [
                        'values' => [
                            'nl' => 'HR100 ketel',
                        ],
                        'order' => 3,
                        'calculate_value' => 3,
                        'is_default' => false,
                    ],
                    [
                        'values' => [
                            'nl' => 'HR104 ketel',
                        ],
                        'order' => 4,
                        'calculate_value' => 4,
                        'is_default' => false,
                    ],
                    [
                        'values' => [
                            'nl' => 'HR107 ketel',
                        ],
                        'order' => 5,
                        'calculate_value' => 5,
                        'is_default' => true,
                    ],
                ],
            ],
//            [
//                'names' => [
//                    'nl' => 'Douche wtw',
//                ],
//                'service_type' => 'Heating',
//                'order' => 0,
//                'info' => [
//                    'nl' => 'Infotext hier',
//                ],
//                'service_values' => [
//                    [
//                        'values' => [
//                            'nl' => 'Geen',
//                        ],
//                        'order' => 1,
//                        'calculate_value' => 1,
//                    ],
//                    [
//                        'values' => [
//                            'nl' => 'Aanwezig',
//                        ],
//                        'order' => 2,
//                        'calculate_value' => 2,
//                    ],
//                ],
//            ],
            [
                'names' => [
                    'nl' => 'Hoe wordt het huis geventileerd?',
                ],
                'short' => 'house-ventilation',
                'service_type' => 'Ventilation',
                'order' => 0,
                'info' => [
                    'nl' => 'Infotext hier',
                ],
                'service_values' => [
                    [
                        'values' => [
                            'nl' => 'Natuurlijke ventilatie',
                        ],
                        'order' => 1,
                        'calculate_value' => 1,
                    ],
                    [
                        'values' => [
                            'nl' => 'Mechanische ventilatie',
                        ],
                        'order' => 2,
                        'calculate_value' => 2,
                    ],
                    [
                        'values' => [
                            'nl' => 'Gebalanceerde ventilatie',
                        ],
                        'order' => 3,
                        'calculate_value' => 3,
                    ],
                    [
                        'values' => [
                            'nl' => 'Decentrale mechanische ventilatie',
                        ],
                        'order' => 4,
                        'calculate_value' => 4,
                    ],
                ],
            ],
            [
                'names' => [
                    'nl' => 'Zonnepanelen',
                ],
                'short' => 'total-sun-panels',
                'service_type' => 'PV-wind',
                'order' => 0,
                'info' => [
                    'nl' => 'Infotext hier',
                ],
                'service_values' => [], // no values
            ],
            /*
            [
                'names' => [
                    'nl' => 'PV panelen',
                ],
                'short' => 'pv-panels',
                'service_type' => 'PV-wind',
                'order' => 0,
                'info' => [
                    'nl' => 'Infotext hier',
                ],
                'service_values' => [
                    // the peak powers
                    [
                        'values' => [
                            'nl' => '260'
                        ],
                        'order' => 0,
                        'calculate_value' => 260,
                    ],
                    [
                        'values' => [
                            'nl' => '265'
                        ],
                        'order' => 1,
                        'calculate_value' => 265,
                    ],
                    [
                        'values' => [
                            'nl' => '270'
                        ],
                        'order' => 2,
                        'calculate_value' => 270,
                    ],
                    [
                        'values' => [
                            'nl' => '275'
                        ],
                        'order' => 3,
                        'calculate_value' => 275,
                    ],
                    [
                        'values' => [
                            'nl' => '280'
                        ],
                        'order' => 4,
                        'calculate_value' => 280,
                    ],
                    [
                        'values' => [
                            'nl' => '285'
                        ],
                        'order' => 5,
                        'calculate_value' => 285,
                    ],
                    [
                        'values' => [
                            'nl' => '290'
                        ],
                        'order' => 6,
                        'calculate_value' => 290,
                    ],
                    [
                        'values' => [
                            'nl' => '295'
                        ],
                        'order' => 7,
                        'calculate_value' => 295,
                    ],
                    [
                        'values' => [
                            'nl' => '300'
                        ],
                        'order' => 8,
                        'calculate_value' => 300,
                    ],
                ],
            ],*/
        ];

        foreach ($services as $service) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($service['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            $infoUuid = \App\Helpers\Str::uuid();
            foreach ($service['info'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $infoUuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            $nameUuid = \DB::table('translations')
                ->where('translation', $service['service_type'])
                ->where('language', 'en')
                ->first(['key']);

            // Get the category. If it doesn't exist: create it
            $serviceType = \DB::table('service_types')->where('name', $nameUuid->key)->first();

            if ($serviceType instanceof \stdClass) {
                $serviceId = \DB::table('services')->insertGetId([
                    'name'            => $uuid,
                    'short' => $service['short'],
                    'service_type_id' => $serviceType->id,
                    'order' => $service['order'],
                    'info' => $infoUuid,
                ]);

                foreach ($service['service_values'] as $serviceValue) {
                    $uuid = \App\Helpers\Str::uuid();
                    foreach ($serviceValue['values'] as $locale => $name) {
                        \DB::table('translations')->insert([
                            'key'         => $uuid,
                            'language'    => $locale,
                            'translation' => $name,
                        ]);
                    }

                    \DB::table('service_values')->insert([
                        'service_id' => $serviceId,
                        'value' => $uuid,
                        'order' => $serviceValue['order'],
                        'calculate_value' => isset($serviceValue['calculate_value']) ? $serviceValue['calculate_value'] : null,
                        'is_default' => $serviceValue['is_default'] ?? false,
                    ]);
                }
            }
        }
    }
}
