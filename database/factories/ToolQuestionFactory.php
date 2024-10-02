<?php



namespace Database\Factories;

use App\Helpers\DataTypes\Caster;
use App\Helpers\Str;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use Illuminate\Database\Eloquent\Factories\Factory;

class ToolQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'short' => Str::slug($this->faker->sentence(3)),
            'save_in' => null,
            'for_specific_input_source_id' => null,
            'name' => ['nl' => $this->faker->text(60)],
            'help_text' => ['nl' => $this->faker->text(240)],
            'placeholder' => ['nl' => $this->faker->text(60)],
            'data_type' => Caster::STRING, // Default, like in the DB
            'coach' => $this->faker->boolean(),
            'resident' => $this->faker->boolean(),
            'options' => null,
            'validation' => ['required', 'string'],
            'unit_of_measure' => $this->faker->randomElement(['m2', 'graden']),
        ];
    }

    public function typeArray(): Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'data_type' => Caster::ARRAY,
                'validation' => ['required', 'exists:tool_question_custom_values,short'],
            ];
        })->afterCreating(function (ToolQuestion $toolQuestion) {
            ToolQuestionCustomValue::factory()->count(mt_rand(3, 8))->create([
                'tool_question_id' => $toolQuestion->id,
            ]);
        });
    }
}
