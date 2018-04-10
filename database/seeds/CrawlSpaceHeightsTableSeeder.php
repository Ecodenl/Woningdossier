<?php

use Illuminate\Database\Seeder;

class CrawlSpaceHeightsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $heights = [
            [
                'name' => 'Best hoog (meer dan 50 cm)',
                'calculate_value' => 1,
            ],
            [
                'name' => 'Laag (tussen 30 en 50 cm)',
                'calculate_value' => 2
            ],
            [
                'name' => 'Heel laag (minder dan 30 cm)',
                'calculate_value' => 3
            ],
            [
                'name' => 'Onbekend',
                'calculate_value' => 4
            ],
        ];

        foreach ($heights as $height) {
            \App\Models\CrawlSpaceHeight::create($height);
        }
    }
}
