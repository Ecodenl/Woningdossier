<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoofTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roofTypes = [
            [
                'name' => [
                    'nl' => 'Hellend dak',
                ],
                'order' => 0,
                'calculate_value' => 1,
                'short' => 'pitched',
            ],
            [
                'name' => [
                    'nl' => 'Plat dak',
                ],
                'order' => 2,
                'calculate_value' => 3,
                'short' => 'flat',
            ],
            [
                'name' => [
                    'nl' => 'Geen dak',
                ],
                'order' => 4,
                'calculate_value' => 5,
                'short' => 'none',
            ],
        ];

        foreach ($roofTypes as $roofType) {
            DB::table('roof_types')->insert([
                'calculate_value' => $roofType['calculate_value'],
                'order' => $roofType['order'],
                'name' => json_encode($roofType['name']),
                'short' => $roofType['short'],
            ]);
        }
    }
}
