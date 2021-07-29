<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InterestsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $interests = [
            [
                'name' => [
                    'nl' => 'Ja, op korte termijn',
                ],
                'calculate_value' => 1,
                'order' => 0,
            ],
            [
                'name' => [
                    'nl' => 'Ja, op termijn',
                ],
                'calculate_value' => 2,
                'order' => 1,
            ],
            [
                'name' => [
                    'nl' => 'Misschien, meer informatie gewenst',
                ],
                'calculate_value' => 3,
                'order' => 2,
            ],
            [
                'name' => [
                    'nl' => 'Nee, geen interesse',
                ],
                'calculate_value' => 4,
                'order' => 3,
            ],
            [
                'name' => [
                    'nl' => 'Nee, niet mogelijk / reeds uitgevoerd',
                ],
                'calculate_value' => 5,
                'order' => 4,
            ],
        ];

        foreach ($interests as $interest) {
            DB::table('interests')->insert([
                'name' => json_encode($interest['name']),
                'calculate_value' => $interest['calculate_value'],
                'order' => $interest['order'],
            ]);
        }
    }
}
