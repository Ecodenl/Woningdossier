<?php

use Illuminate\Database\Seeder;

class HouseFramesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $frames = [
            [
                'name' => 'Alleen houten kozijnen',
                'calculate_value' => 1,
            ],
            [
                'name' => 'Houten kozijnen en enkele andere kozijnen (bijvoorbeeld kunststof of aluminium)',
                'calculate_value' => 2,
            ],
            [
                'name' => 'Enkele houten kozijnen, voornamelijk kunststof en of aluminium',
                'calculate_value' => 3,
            ],
            [
                'name' => 'Geen houten kozijnen',
                'calculate_value' => 4,
            ],
            [
                'name' => 'Overig',
                'calculate_value' => 5,
            ],
        ];

        foreach ($frames as $frame) {
            \App\Models\HouseFrame::create($frame);
        }
    }
}
