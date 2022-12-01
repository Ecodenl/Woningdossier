<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class InputSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->word;
    $short = \Illuminate\Support\Str::slug($name);
    return [
        'name' => $name,
        'short' => $short,
        'order' => $this->faker->randomNumber(2),
    ];
    }
}
