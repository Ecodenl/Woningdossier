<?php

namespace Database\Factories;

use App\Models\ExampleBuildingContent;
use Illuminate\Database\Eloquent\Factories\Factory;

class ExampleBuildingFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $buildingTypes = [
            'Hoekwoning', 'Tussenwoning', 'Benedenwoning', 'Bovenwoning', 'Vrijstaand', '2 onder 1 kap',
            'Appartement',
        ];

        return [
            'name' => json_encode(['nl' => $this->faker->randomElement($buildingTypes)]),
            'building_type_id' => null,
            'cooperation_id' => null,
            'order' => $this->faker->randomDigitNotZero(),
            'is_default' => $this->faker->boolean,
        ];
    }

    public function withContents(): ExampleBuildingFactory
    {
        return $this->has(
            ExampleBuildingContent::factory()->count($this->faker->numberBetween(2, 5)),
            'contents',
        );
    }
}
