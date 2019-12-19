<?php

namespace App\Services;

use App\Helpers\RegistrationHelper;
use App\Models\Account;

class AccountService
{

    public static function create($email, $password)
    {
        return Account::create([
            'email' => $email,
            'password' => \Hash::make($password),
            'confirm_token' => RegistrationHelper::generateConfirmToken(),
        ]);
    }
}