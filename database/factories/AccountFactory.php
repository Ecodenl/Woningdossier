<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class AccountFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $password;

    return [
        'email' => $this->faker->email,
        'password' => $password ?: Hash::make('secret'),
        'email_verified_at' => now(),
        'active' => true,
        'is_admin' => mt_rand(0, 1),
    ];
    }
}
