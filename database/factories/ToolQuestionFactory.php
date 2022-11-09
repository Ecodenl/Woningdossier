<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionType;

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
        'name' => json_encode(['nl' => $this->faker->text(60)]),
        'help_text' => json_encode(['nl' => $this->faker->text(240)]),
        'save_in' => null,
        'unit_of_measure' => $this->faker->randomElement(['m2', 'graden']),
        'coach' => $this->faker->boolean,
        'resident' => $this->faker->boolean,
    ];
    }
}
