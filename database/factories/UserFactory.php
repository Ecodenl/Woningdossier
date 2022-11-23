<?php

namespace Database\Factories;

use App\Helpers\RoleHelper;
use App\Models\Building;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'account_id' => \App\Models\Account::factory(),
            'cooperation_id' => \App\Models\Cooperation::factory(),
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'phone_number' => $this->faker->phoneNumber,
            'allow_access' => $this->faker->boolean,
        ];
    }

    public function asCoach()
    {
        return $this->state(function (array $attributes) {
            return [
                // We need this state for an after hook
            ];
        })->afterCreating(fn(User $user) => $user->assignRole(RoleHelper::ROLE_COACH));
    }

    public function asResident()
    {
        return $this->state(function (array $attributes) {
            return [
                // We need this state for an after hook
            ];
        })->afterCreating(fn(User $user) => $user->assignRole(RoleHelper::ROLE_RESIDENT));
    }


    public function configure()
    {
        return $this->afterCreating(function (User $user) {
            // Ensure we always have a building
            $building = Building::factory()->create(['user_id' => $user]);
        });
    }
}



