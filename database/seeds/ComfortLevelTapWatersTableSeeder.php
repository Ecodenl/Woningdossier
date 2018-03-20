<?php

use Illuminate\Database\Seeder;
use \App\Models\ComfortLevelTapWater;

class ComfortLevelTapWatersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $comforts = [
            'standaard',
            'comfort',
            'comfort plus',
        ];

        foreach ($comforts as $comfort) {
            ComfortLevelTapWater::create(['name' => $comfort]);
        }
    }
}
