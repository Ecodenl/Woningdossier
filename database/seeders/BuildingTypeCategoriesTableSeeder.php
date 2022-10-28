<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BuildingTypeCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $buildingTypeCategories = [
            [
                'name' => [
                    'nl' => 'Appartement'
                ],
                'short' => 'apartment'
            ],
            [
                'name' => [
                    'nl' => 'Hoekwoning'
                ],
                'short' => 'corner-house'
            ],
            [
                'name' => [
                    'nl' => 'Vrijstaand'
                ],
                'short' => 'detached-house'
            ],
            [
                'name' => [
                    'nl' => '2 onder 1 kap',
                ],
                'short' => '2-homes-under-1-roof'
            ],
            [
                'name' => [
                    'nl' => 'Tussenwoning'
                ],
                'short' => 'terraced-house'
            ],
        ];

        foreach ($buildingTypeCategories as $buildingTypeCategory) {
            DB::table('building_type_categories')->updateOrInsert(
                [
                    'short' => $buildingTypeCategory['short']
                ],
                [
                    'name' => json_encode($buildingTypeCategory['name'])
                ]
            );
        }
    }
}
