<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class BuildingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'street' => $this->faker->streetName,
        'number' => $this->faker->numberBetween(3, 22),
        'city' => $this->faker->city,
        'postal_code' => $this->faker->postcode,
        'country_code' => $this->faker->countryCode,
        'owner' => $this->faker->boolean,
        'primary' => $this->faker->boolean,
        'user_id' => \App\Models\User::factory(),
    ];
    }
}
