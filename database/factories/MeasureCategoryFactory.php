<?php


namespace Database\Factories;

use App\Helpers\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class MeasureCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $name = $this->faker->randomElement(['Isolatie', 'Aardgas vrije woning', 'Besparing']);

        return [
            'name' => ['nl' => $name],
            'short' => Str::slug($name),
        ];
    }
}
