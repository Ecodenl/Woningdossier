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
                'name' => 'Puntdak',
                'order' => 0,
                'calculate_value' => 2,
                'short' => 'gabled-roof',
            ],
            [
                'name' => 'Hellend dak',
                'order' => 1,
                'calculate_value' => 1,
                'short' => 'pitched',
            ],
            [
                'name' => 'Plat dak',
                'order' => 2,
                'calculate_value' => 3,
                'short' => 'flat',
            ],
            [
                'name' => 'Plat + hellend dak',
                'order' => 3,
                'calculate_value' => 4,
                'short' => 'flat-pitched-roof',
            ],
            [
                'name' => 'Afgerond dak',
                'order' => 4,
                'calculate_value' => 5,
                'short' => 'rounded-roof',
            ],
            [
                'name' => 'Rieten dak',
                'order' => 5,
                'calculate_value' => 6,
                'short' => 'straw-roof',
            ],
            [
                'name' => 'Geen dak',
                'order' => 6,
                // the old one
                'calculate_value' => 5,
                'new_calculate_value' => 100,
                'short' => 'none',
            ],
        ];

        foreach ($roofTypes as $roofType) {
            DB::table('roof_types')->updateOrInsert(
                [
                    'calculate_value' => $roofType['calculate_value'],
                ],
                [
                    'calculate_value' => $roofType['new_calculate_value'] ?? $roofType['calculate_value'],
                    'order' => $roofType['order'],
                    'name' => json_encode(['nl' => $roofType['name']]),
                    'short' => $roofType['short'],
                ]
            );
        }
    }
}
