<?php


namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CooperationMeasureApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => ['nl' => $this->faker->randomElement(['Vloertje', 'Bakstel', 'Nieuwe lampen', 'Keuken', 'Badkamer'])],
            'info' => ['nl' => $this->faker->realText(150)],
            'costs' => [
                'from' => $this->faker->numberBetween(100, 1000),
                'to' => $this->faker->numberBetween(1000, 10000),
            ],
            'savings_money' => $this->faker->randomFloat(2, 0, 500),
            'extra' => [
                'icon' => 'icon-tools',
            ],
            'is_extensive_measure' => $this->faker->boolean(),
            'is_deletable' => $this->faker->boolean(),
        ];
    }
}
