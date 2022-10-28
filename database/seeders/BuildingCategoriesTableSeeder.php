<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'type' => 'BLDNGCAT_RES_SINGLE',
                'name' => [
                    'en' => 'Single-family houses of different types',
                ],
            ],
            [
                'type' => 'BLDNGCAT_RES_APPBLOCK',
                'name' => [
                    'en' => 'Apartment blocks',
                ],
            ],
            [
                'type' => 'BLDNGCAT_RES_ELDER',
                'name' => [
                    'en' =>'Homes for elderly and disabled people',
                ],
            ],
            [
                'type' => 'BLDNGCAT_RES_COLL',
                'name' => [
                    'en' =>'Residence for collective use',
                ],
            ],
            [
                'type' => 'BLDNGCAT_RES_MOBIL',
                'name' => [
                    'en' =>'Mobile home',
                ],
            ],
            [
                'type' => 'BLDNGCAT_RES_HOL',
                'name' => [
                    'en' =>'Holiday home',
                ],
            ],
            [
                'type' => 'BLDNGCAT_OFF',
                'name' => [
                    'en' =>'Offices',
                ],
            ],
            [
                'type' => 'BLDNGCAT_EDUC',
                'name' => [
                    'en' =>'Educational buildings',
                ],
            ],
            [
                'type' => 'BLDNGCAT_HOSP',
                'name' => [
                    'en' =>'Hospitals',
                ],
            ],
            [
                'type' => 'BLDNGCAT_HOTEL',
                'name' => [
                    'en' =>'Hotels and restaurants',
                ],
            ],
            [
                'type' => 'BLDNGCAT_SPORT',
                'name' => [
                    'en' =>'Sports facilities',
                ],
            ],
            [
                'type' => 'BLDNGCAT_RETAIL',
                'name' => [
                    'en' =>'Wholesale and retail trade services buildings',
                ],
            ],
            [
                'type' => 'BLDNGCAT_DATA_CENTER',
                'name' => [
                    'en' =>'Data centre',
                ],
            ],
            [
                'type' => 'BLDNGCAT_INDUS',
                'name' => [
                    'en' =>'Industrial sites',
                ],
            ],
            [
                'type' => 'BLDNGCAT_WORKS',
                'name' => [
                    'en' =>'Workshops',
                ],
            ],
        ];

        foreach ($categories as $category) {
            DB::table('building_categories')->insert([
                'type' => $category['type'],
                'name' => json_encode($category['name']),
            ]);
        }
    }
}
