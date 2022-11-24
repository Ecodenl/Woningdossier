<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Model;

class CustomMeasureApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'name' => ['nl' => $this->faker->randomElement(['Vloertje', 'Bakstel', 'Nieuwe lampen'])],
        'info' => ['nl' => $this->faker->randomElement(['Vloertje', 'Bakstel', 'Nieuwe lampen'])],
        'extra' => ['icon' => 'icon-tools'],
    ];
    }
}
