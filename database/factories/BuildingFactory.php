<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuildingFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'municipality_id' => null,
            'street' => $this->faker->streetName(),
            'number' => $this->faker->numberBetween(3, 22),
            'extension' => '',
            'city' => 'bubba',
            'postal_code' => $this->faker->postcode(),
            'owner' => $this->faker->boolean(),
            'primary' => $this->faker->boolean(),
            'bag_addressid' => '',
            'bag_woonplaats_id' => null,
        ];
    }

    public function withUser(): BuildingFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'user_id' => User::factory()->withAccount()->withCooperation()->create()->id,
            ];
        });
    }
}
