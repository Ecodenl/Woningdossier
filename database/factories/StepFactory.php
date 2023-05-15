<?php

namespace Database\Factories;

use App\Models\Step;
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
            'parent_id' => null,
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'short' => \Illuminate\Support\Str::slug($name),
            'order' => $this->faker->randomNumber(1),
        ];
    }

    public function withParent(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'parent_id' => Step::factory()->create()->id,
            ];
        });
    }
}
