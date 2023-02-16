<?php


namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomMeasureApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => ['nl' => $this->faker->randomElement(['Vloertje', 'Bakstel', 'Nieuwe lampen'])],
            'info' => ['nl' => $this->faker->randomElement(['Vloertje', 'Bakstel', 'Nieuwe lampen'])],
            'hash' => $this->faker->uuid,
        ];
    }
}
