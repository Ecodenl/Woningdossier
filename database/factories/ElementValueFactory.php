<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ElementValueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'element_id' => \App\Models\Element::factory(),
        'value' => json_encode(['nl' => $this->faker->word]),
        'calculate_value' => $this->faker->randomFloat(2, 0, 100),
        'order' => $this->faker->randomNumber(2),
    ];
    }
}
