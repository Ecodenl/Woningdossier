<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BuildingElementFactory extends Factory
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
        'element_id' => \App\Models\Element::factory(),
        'element_value_id' => \App\Models\ElementValue::factory(),
        'extra' => null,
    ];
    }
}
