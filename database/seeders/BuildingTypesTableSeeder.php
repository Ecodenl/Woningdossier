<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BuildingTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $apartmentCategory = DB::table('building_type_categories')
            ->where('short', 'apartment')->first();

        $cornerHouse = DB::table('building_type_categories')
            ->where('short', 'corner-house')->first();

        $detachedHouse = DB::table('building_type_categories')
            ->where('short', 'detached-house')->first();

        $twoRoofsUnderOneRoof = DB::table('building_type_categories')
            ->where('short', '2-homes-under-1-roof')->first();


        $terracedHouse = DB::table('building_type_categories')
            ->where('short', 'terraced-house')->first();


        $buildingTypes = [
            $detachedHouse->id => [
                [
                    'name' => [
                        'nl' => 'Vrijstaande woning',
                    ],
                    'calculate_value' => 2,
                ],
            ],
            $twoRoofsUnderOneRoof->id => [
                [
                    'name' => [
                        'nl' => '2 onder 1 kap',
                    ],
                    'calculate_value' => 3,
                ],
            ],
            $cornerHouse->id => [
                [
                    'name' => [
                        'nl' => 'Hoekwoning',
                    ],
                    'calculate_value' => 4,
                ],
            ],
            $terracedHouse->id => [

                [
                    'name' => [
                        'nl' => 'Tussenwoning',
                    ],
                    'calculate_value' => 5,
                ],
            ],
            $apartmentCategory->id => [
                [
                    'name' => [
                        'nl' => 'Benedenwoning hoek',
                    ],
                    'calculate_value' => 6,
                ],
                [
                    'name' => [
                        'nl' => 'Benedenwoning tussen',
                    ],
                    'calculate_value' => 7,
                ],
                [
                    'name' => [
                        'nl' => 'Bovenwoning hoek',
                    ],
                    'calculate_value' => 8,
                ],
                [
                    'name' => [
                        'nl' => 'Bovenwoning tussen',
                    ],
                    'calculate_value' => 9,
                ],
                [
                    'name' => [
                        'nl' => 'Appartement tussen op een tussenverdieping',
                    ],
                    'calculate_value' => 10,
                ],
                [
                    'name' => [
                        'nl' => 'Appartement hoek op een tussenverdieping',
                    ],
                    'calculate_value' => 11,
                ],
            ]
        ];

        foreach ($buildingTypes as $buildingTypeCategoryId => $buildingType) {
            foreach ($buildingType as $data) {


                DB::table('building_types')->updateOrInsert(
                    [
                        'calculate_value' => $data['calculate_value'],
                    ],
                    [
                        'name' => json_encode($data['name']),
                        'building_type_category_id' => $buildingTypeCategoryId,
                    ],
                );
            }
        }
    }
}