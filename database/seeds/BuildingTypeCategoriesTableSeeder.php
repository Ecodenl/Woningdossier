<?php

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
        $buildingTYpeCategories = [
            [
                'name' => [
                    'nl' => 'Appartement'
                ],
                'short' => 'apartment'
            ],
        ];

        foreach ($buildingTYpeCategories as $buildingTypeCategory) {
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
