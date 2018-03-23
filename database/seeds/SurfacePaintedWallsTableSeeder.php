<?php

use Illuminate\Database\Seeder;

class SurfacePaintedWallsTableSeeder extends Seeder
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
                'name' => 'Tot 10 m2',
                'calculate_value' => 1,
            ],
            [
                'name' => '10 m2 to 25 m2',
                'calculate_value' => 2,
            ],
            [
                'name' => '25 m2 tot 50 m2',
                'calculate_value' => 3,
            ],
            [
                'name' => '50 m2 tot 80 m2',
                'calculate_value' => 4,
            ],
            [
                'name' => 'Meer dan 80 m2',
                'calculate_value' => 5,
            ],
        ];

        foreach ($surfaces as $surface) {
            \App\Models\SurfacePaintedWall::create([
                'name' => $surface['name'],
                'calculate_value' => $surface['calculate_value'],
            ]);
        }
    }
}
