<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ElementFactory extends Factory
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
        'name' => json_encode(['nl' => $name]),
        'short' => $short,
        'service_type_id' => \App\Models\ServiceType::factory(),
        'order' => $this->faker->randomNumber(2),
        'info' => json_encode(['nl' => $this->faker->sentence]),
    ];
    }
}
