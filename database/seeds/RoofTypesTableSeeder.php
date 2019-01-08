<?php

use Illuminate\Database\Seeder;

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
                'names' => [
                    'nl' => 'Hellend dak',
                ],
                'order' => 0,
                'calculate_value' => 1,
                'short' => 'pitched',
            ],
            [
                'names' => [
                    'nl' => 'Plat dak',
                ],
                'order' => 2,
                'calculate_value' => 3,
                'short' => 'flat',
            ],
            [
                'names' => [
                    'nl' => 'Geen dak',
                ],
                'order' => 4,
                'calculate_value' => 5,
                'short' => 'none',
            ],
        ];

        foreach ($roofTypes as $roofType) {
            $uuid = \App\Helpers\Str::uuid();
            foreach ($roofType['names'] as $locale => $name) {
                \DB::table('translations')->insert([
                    'key'         => $uuid,
                    'language'    => $locale,
                    'translation' => $name,
                ]);
            }

            \DB::table('roof_types')->insert([
                'calculate_value' => $roofType['calculate_value'],
                'order' => $roofType['order'],
                'name' => $uuid,
                'short' => $roofType['short'],
            ]);
        }
    }
}
