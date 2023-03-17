<?php


namespace Database\Factories;

use App\Services\UserActionPlanAdviceService;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserActionPlanAdviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            //'user_id' => null,
            //'input_source_id' => null,
            //'user_action_plan_advisable_type' => null,
            //'user_action_plan_advisable_id' => null,
            'category' => $this->faker->randomElement(UserActionPlanAdviceService::getCategories()),
            'visible' => $this->faker->boolean,
            'subsidy_available' => $this->faker->boolean,
            'loan_available' => $this->faker->boolean,
            'order' => $this->faker->randomDigit(),
            'costs' => [
                'from' => $this->faker->numberBetween(100, 1000),
                'to' => $this->faker->numberBetween(1000, 10000),
            ],
            'savings_gas' => $this->faker->boolean ? $this->faker->randomFloat(2, 0, 500) : null,
            'savings_electricity' => $this->faker->boolean ? $this->faker->randomFloat(2, 0, 500) : null,
            'savings_money' => $this->faker->boolean ? $this->faker->randomFloat(2, 0, 500) : null,
            'year' => $this->faker->boolean ? $this->faker->year : null,
            'planned' => $this->faker->boolean,
            'planned_year' => $this->faker->boolean ? $this->faker->year : null,
            //'step_id' => null,
        ];
    }
}
