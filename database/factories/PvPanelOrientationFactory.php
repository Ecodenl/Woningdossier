<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PvPanelOrientationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->word;

    return [
        'name' => json_encode(['nl' => $name]),
        'short' => strtolower($name[0]),
        'order' => $this->faker->randomNumber(2),
    ];
    }
}
