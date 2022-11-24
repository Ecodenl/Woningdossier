<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CooperationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name = $this->faker->randomElement(['Groen', 'CO2', 'Meter op null']);

    return [
        'name' => $name,
        'slug' => \Illuminate\Support\Str::slug($name),
        'website_url' => $this->faker->url,
        'cooperation_email' => $this->faker->email,
    ];
    }
}
