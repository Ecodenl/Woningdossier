<?php

namespace App\Services;

use App\Models\Account;
use Illuminate\Support\Facades\Hash;

class AccountService
{
    public static function create($email, $password)
    {
        return Account::create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);
    }
}
