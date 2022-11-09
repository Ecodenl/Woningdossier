<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BuildingHeaterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'building_id' => \App\Models\Building::factory(),
        'input_source_id' => \App\Models\InputSource::factory(),
        'pv_panel_orientation_id' => \App\Models\PvPanelOrientation::factory(),
        'angle' => 10 * mt_rand(1, 9),
    ];
    }
}
