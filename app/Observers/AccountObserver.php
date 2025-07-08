<?php

namespace App\Observers;

use App\Models\Account;

class AccountObserver
{
    public function saved(Account $account)
    {
        \App\Helpers\Cache\Account::wipe($account);
    }

    public function deleted(Account $account): void
    {
        \App\Helpers\Cache\Account::wipe($account);
    }
}
