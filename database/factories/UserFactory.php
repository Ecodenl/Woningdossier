<?php

namespace Database\Factories;

use App\Helpers\RoleHelper;
use App\Models\Account;
use App\Models\Building;
use App\Models\Cooperation;
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
     */
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'phone_number' => $this->faker->phoneNumber(),
            'allow_access' => $this->faker->boolean(),
        ];
    }

    public function asCoach(): UserFactory
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole(RoleHelper::ROLE_COACH));
    }

    public function asResident(): UserFactory
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole(RoleHelper::ROLE_RESIDENT));
    }

    public function asCooperationAdmin(): UserFactory
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole(RoleHelper::ROLE_COOPERATION_ADMIN));
    }

    public function asCoordinator(): UserFactory
    {
        return $this->afterCreating(fn (User $user) => $user->assignRole(RoleHelper::ROLE_COORDINATOR));
    }

    public function withAccount(): UserFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'account_id' => Account::factory()->create()->id,
            ];
        });
    }

    public function withCooperation(): UserFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'cooperation_id' => Cooperation::factory()->create()->id,
            ];
        });
    }

    public function withBuilding(): UserFactory
    {
        return $this->afterCreating(fn (User $user) => Building::factory()->create(['user_id' => $user->id]));
    }
}
