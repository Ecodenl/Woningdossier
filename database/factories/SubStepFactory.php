<?php

namespace Database\Factories;

use App\Helpers\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubStepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->word;

        return [
            'name' => json_encode([
                'nl' => $name,
            ]),
            'slug' => json_encode([
                'nl' => Str::slug($name),
            ]),
            'order' => $this->faker->randomNumber(1),
            'conditions' => null,
            //'step_id'
            //'sub_step_template_id'
        ];
    }
}
