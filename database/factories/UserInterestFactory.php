<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserInterestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $model = $this->faker->randomElement([
//        \App\Models\MeasureApplication::class,
        \App\Models\Step::class,
        \App\Models\Element::class,
    ]);

    return [
        'user_id' => \App\Models\User::factory(),
        'input_source_id' => \App\Models\InputSource::factory(),
        'interested_in_type' => $model,
        'interested_in_id' => factory($model),
        'interest_id' => \App\Models\Interest::factory(),
    ];
    }
}
