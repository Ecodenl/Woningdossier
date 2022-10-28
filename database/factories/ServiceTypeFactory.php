<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'name' => json_encode(['nl' => $this->faker->word]),
        'iso' => "M" . $this->faker->randomDigit,
    ];
    }
}
