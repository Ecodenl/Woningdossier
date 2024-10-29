<?php



namespace Database\Factories;

use App\Helpers\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ToolQuestionCustomValueFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            //'tool_question_id'
            'short' => Str::slug($this->faker->sentence(3)),
            'name' => ['nl' => $this->faker->text(60)],
            'show' => true,
            'order' => $this->faker->randomDigit(),
            'extra' => [],
            'conditions' => [],
        ];
    }
}
