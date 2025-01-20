<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionnaireFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'cooperation_id' => null,
            'name' => [
                'nl' => $this->faker->text(80),
            ],
        ];
    }

    public function withCooperation()
    {
        return $this->state(function (array $attributes) {
            return [
                'cooperation_id' => \App\Models\Cooperation::factory(),
            ];
        });
    }
}
