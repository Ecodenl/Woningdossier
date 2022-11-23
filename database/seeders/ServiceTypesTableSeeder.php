<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            [
                'name' => [
                    'en' => 'Heating',
                ],
                'iso' => 'M3',
            ],
            [
                'name' => [
                    'en' => 'Cooling',
                ],
                'iso' => 'M4',
            ],
            [
                'name' => [
                    'en' => 'Ventilation',
                ],
                'iso' => 'M5',
            ],
            [
                'name' => [
                    'en' => 'Humidification',
                ],
                'iso' => 'M6',
            ],
            [
                'name' => [
                    'en' => 'Dehumidification',
                ],
                'iso' => 'M7',
            ],
            [
                'name' => [
                    'en' => 'Domestic hot water',
                ],
                'iso' => 'M8',
            ],
            [
                'name' => [
                    'en' => 'Lighting',
                ],
                'iso' => 'M9',
            ],
            [
                'name' => [
                    'en' => 'External lighting',
                ],
                'iso' => '',
            ],
            [
                'name' => [
                    'en' => 'Building automation and control',
                ],
                'iso' => 'M10',
            ],
            [
                'name' => [
                    'en' => 'People transport',
                ],
                'iso' => '',
            ],
            [
                'name' => [
                    'en' => 'PV-wind',
                ],
                'iso' => 'M11',
            ],
            [
                'name' => [
                    'en' => 'Appliances',
                ],
                'iso' => '',
            ],
            [
                'name' => [
                    'en' => 'Others',
                ],
                'iso' => '',
            ],
        ];

        foreach ($types as $type) {
            DB::table('service_types')->insert([
                'iso' => $type['iso'],
                'name' => json_encode($type['name']),
            ]);
        }
    }
}
