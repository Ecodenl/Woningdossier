<?php

namespace Database\Factories;

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
}
$factory->afterCreating(User::class, function ($user, $faker) {
    // Ensure we always have a building
    $building = factory(Building::class)->create(['user_id' => $user]);
});

$factory->state(User::class, 'asCoach', function ($faker) {
    return [
        // We need this state for an after hook
    ];
});

$factory->state(User::class, 'asResident', function ($faker) {
    return [
        // We need this state for an after hook
    ];
});

$factory->afterCreatingState(User::class, 'asCoach', function ($user, $faker) {
    // Find and attach the coach role
    $user->assignRole(RoleHelper::ROLE_COACH);
});

$factory->afterCreatingState(User::class, 'asResident', function ($user, $faker) {
    // Find and attach the resident role
    $user->assignRole(RoleHelper::ROLE_RESIDENT);
});
