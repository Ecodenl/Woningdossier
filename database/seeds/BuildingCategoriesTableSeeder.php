<?php

use Illuminate\Database\Seeder;

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
                'names' => [
                    'en' => 'Single-family houses of different types',
                ],
            ],
            [
                'type' => 'BLDNGCAT_RES_APPBLOCK',
                'names' => [
                    'en' => 'Apartment blocks',
                ],
            ],
            [
                'type' => 'BLDNGCAT_RES_ELDER',
                'names' => [
                    'en' =>'Homes for elderly and disabled people',
                ],
            ],
            [
                'type' => 'BLDNGCAT_RES_COLL',
                'names' => [
                    'en' =>'Residence for collective use',
                ],
            ],
            [
                'type' => 'BLDNGCAT_RES_MOBIL',
                'names' => [
                    'en' =>'Mobile home',
                ],
            ],
            [
                'type' => 'BLDNGCAT_RES_HOL',
                'names' => [
                    'en' =>'Holiday home',
                ],
            ],
            [
                'type' => 'BLDNGCAT_OFF',
                'names' => [
                    'en' =>'Offices',
                ],
            ],
            [
                'type' => 'BLDNGCAT_EDUC',
                'names' => [
                    'en' =>'Educational buildings',
                ],
            ],
            [
                'type' => 'BLDNGCAT_HOSP',
                'names' => [
                    'en' =>'Hospitals',
                ],
            ],
            [
                'type' => 'BLDNGCAT_HOTEL',
                'names' => [
                    'en' =>'Hotels and restaurants',
                ],
            ],
            [
                'type' => 'BLDNGCAT_SPORT',
                'names' => [
                    'en' =>'Sports facilities',
                ],
            ],
            [
                'type' => 'BLDNGCAT_RETAIL',
                'names' => [
                    'en' =>'Wholesale and retail trade services buildings',
                ],
            ],
            [
                'type' => 'BLDNGCAT_DATA_CENTER',
                'names' => [
                    'en' =>'Data centre',
                ],
            ],
            [
                'type' => 'BLDNGCAT_INDUS',
                'names' => [
                    'en' =>'Industrial sites',
                ],
            ],
            [
                'type' => 'BLDNGCAT_WORKS',
                'names' => [
                    'en' =>'Workshops',
                ],
            ],
        ];

        foreach ($categories as $category) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($category['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                'key' => $uuid,
                'language' => $locale,
                'translation' => $name,
            ]);
            }

            \DB::table('building_categories')->insert([
                'type' => $category['type'],
                'name' => $uuid,
            ]);
        }
    }
}
