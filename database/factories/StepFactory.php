<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class StepFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->word();
    return [
        'parent_id' => mt_rand(0, 3) == 0 ? \App\Models\Step::factory() : null,
        'name' => $name,
        'slug' => \Illuminate\Support\Str::slug($name),
        'short' => \Illuminate\Support\Str::slug($name),
        'order' => $this->faker->randomNumber(1),
    ];
    }
}
