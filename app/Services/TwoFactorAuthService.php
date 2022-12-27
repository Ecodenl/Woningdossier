<?php

namespace App\Services;

use App\Models\Account;

class TwoFactorAuthService {

    public function disableTwoFactorAuthentication(Account $account)
    {
        $account->two_factor_secret = null;
        $account->two_factor_confirmed_at = null;
        $account->two_factor_recovery_codes = null;
        $account->save();
    }
}