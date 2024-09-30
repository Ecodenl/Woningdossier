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
    public function definition(): array
    {
        $name = $this->faker->word();

        return [
            'name' => ['nl' => $name],
            'short' => strtolower($name[0]),
            'order' => $this->faker->randomNumber(2),
        ];
    }
}
