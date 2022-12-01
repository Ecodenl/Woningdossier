<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionnaireFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'cooperation_id' => \App\Models\Cooperation::factory(),
        'step_id' => \App\Models\Step::factory(),
        'order' => 0,
        'name' => [
            'nl' => $this->faker->text(80),
        ],
    ];
    }
}
