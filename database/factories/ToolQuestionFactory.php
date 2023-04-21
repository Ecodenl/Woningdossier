<?php



namespace Database\Factories;

use App\Helpers\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ToolQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'short' => Str::slug($this->faker->sentence(3)),
            'save_in' => null,
            'for_specific_input_source_id' => null,
            'name' => ['nl' => $this->faker->text(60)],
            'help_text' => ['nl' => $this->faker->text(240)],
            'placeholder' => ['nl' => $this->faker->text(60)],
            'data_type' => 'string', // Default, like in the DB
            'coach' => $this->faker->boolean,
            'resident' => $this->faker->boolean,
            'options' => null,
            'validation' => ['required', 'string'],
            'unit_of_measure' => $this->faker->randomElement(['m2', 'graden']),
        ];
    }
}
