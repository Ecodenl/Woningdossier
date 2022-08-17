<?php

use App\Helpers\RoleHelper;
use App\Models\Building;
use App\Models\User;
use Faker\Generator as Faker;

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

$factory->define(User::class, function (Faker $faker) {
    return [
        'account_id' => factory(\App\Models\Account::class),
        'cooperation_id' => factory(\App\Models\Cooperation::class),
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'phone_number' => $faker->phoneNumber,
        'allow_access' => $faker->boolean,
    ];
});

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