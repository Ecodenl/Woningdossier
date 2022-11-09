<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Model;

class BuildingVentilationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
        'how' => $this->faker->randomElements(["windows", "windows-doors", "other"]),
        'living_situation' => $this->faker->randomElements(["dry-laundry", "fireplace", "combustion-device"]),
    ];
    }
}
