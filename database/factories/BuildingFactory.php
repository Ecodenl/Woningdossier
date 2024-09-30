<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuildingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'street' => $this->faker->streetName(),
            'number' => $this->faker->numberBetween(3, 22),
            'city' => 'bubba',
            'postal_code' => $this->faker->postcode(),
            'owner' => $this->faker->boolean(),
            'primary' => $this->faker->boolean(),
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
