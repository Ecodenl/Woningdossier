<?php

namespace App\Observers;

use App\Models\Account;

class AccountObserver
{

    public function saved(Account $account)
    {
        \App\Helpers\Cache\Account::wipe($account->id);
    }

    public function deleted(Account $account)
    {
        \App\Helpers\Cache\Account::wipe($account->id);
    }
}