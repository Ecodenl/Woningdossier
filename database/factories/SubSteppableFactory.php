<?php



namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SubSteppableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'order' => $this->faker->randomDigit(),
            //'sub_steppable_id'
            //'sub_steppable_type'
            //'tool_question_type_id'
            'conditions' => null,
            'size' => $this->faker->randomElement(['col-span-6', 'col-span-3', 'col-span-2']),
        ];
    }
}
