<?php

use Illuminate\Database\Seeder;

class HeatSourcesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $heatSources = [
            [
                'name' => 'Buitenlucht',
                'calculate_value' => 1,
            ],
            [
                'name' => 'Bodemwisselaar',
                'calculate_value' => 2,
            ],
        ];

        foreach ($heatSources as $heatSource) {
            \App\Models\HeatSource::create($heatSource);
        }
    }
}
