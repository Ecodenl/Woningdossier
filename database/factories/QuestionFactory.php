<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'type' => 'text',
        'name' => [
            'nl' => $this->faker->text(80),
        ],
        'required' => $this->faker->boolean,
        'validation' => [],
    ];
    }
}
