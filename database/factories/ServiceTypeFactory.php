<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => ['nl' => $this->faker->word()],
            'iso' => "M" . $this->faker->randomDigit(),
        ];
    }
}
