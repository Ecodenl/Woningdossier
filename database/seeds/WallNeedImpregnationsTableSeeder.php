<?php

use Illuminate\Database\Seeder;

class WallNeedImpregnationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $surfaces = [
            [
                'name' => 'Nee',
                'calculate_value' => 0,
            ],
            [
                'name' => 'Ja, tot 10 m2',
                'calculate_value' => 1,
            ],
            [
                'name' => 'Ja, 10 m2 to 25 m2',
                'calculate_value' => 2,
            ],
            [
                'name' => 'Ja, 25 m2 tot 50 m2',
                'calculate_value' => 3,
            ],
            [
                'name' => 'Ja, 50 m2 tot 80 m2',
                'calculate_value' => 4,
            ],
            [
                'name' => 'Ja, meer dan 80 m2',
                'calculate_value' => 5,
            ],
        ];

        foreach ($surfaces as $surface) {
            \App\Models\WallNeedImpregnation::create([
                'name' => $surface['name'],
                'calculate_value' => $surface['calculate_value'],
            ]);
        }
    }
}
