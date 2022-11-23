<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeasureCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $measureCategories = [
            [
                'name' => [
                    'nl' => 'Vloerisolatie',
                ],
            ],
            [
                'name' => [
                    'nl' => 'Gevelisolatie',
                ],
            ],
            [
                'name' => [
                    'nl' => 'Dakisolatie',
                ],
            ],
            [
                'name' => [
                    'nl' => 'Isolatieglas',
                ],
            ],
            [
                'name' => [
                    'nl' => 'Kierdichting',
                ],
            ],
            [
                'name' => [
                    'nl' => 'Ventilatie',
                ],
            ],
            [
                'name' => [
                    'nl' => 'Cv-ketel',
                ],
            ],
            [
                'name' => [
                    'nl' => 'Warmtepomp',
                ],
            ],
            [
                'name' => [
                    'nl' => 'Biomassa',
                ],
            ],
            [
                'name' => [
                    'nl' => 'Warmte afgifte',
                ],
            ],
            [
                'name' => [
                    'nl' => 'Zonnepanelen',
                ],
            ],
            [
                'name' => [
                    'nl' => 'Zonneboiler',
                ],
            ],
            [
                'name' => [
                    'nl' => 'PVT',
                ],
            ],
            [
                'name' => [
                    'nl' => 'Opslag',
                ],
            ],
            [
                'name' => [
                    'nl' => 'Overig',
                ],
            ],
        ];

        foreach ($measureCategories as $measureCategory) {
            DB::table('measure_categories')->insert([
                'name' => json_encode($measureCategory['name']),
            ]);
        }
    }
}
