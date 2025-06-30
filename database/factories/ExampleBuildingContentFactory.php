<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ExampleBuildingContentFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'example_building_id' => null,
            'build_year' => $this->faker->numberBetween(1800, now()->year),
            'content' => json_encode([
                // Just something to fill
                'surface' => 100,
            ]),
        ];
    }

}
