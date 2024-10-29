<?php

namespace Database\Factories;

use App\Helpers\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubStepFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $name = $this->faker->word();

        return [
            'name' => [
                'nl' => $name,
            ],
            'slug' => [
                'nl' => Str::slug($name),
            ],
            'order' => $this->faker->randomNumber(1),
            'conditions' => null,
            //'step_id'
            //'sub_step_template_id'
        ];
    }
}
