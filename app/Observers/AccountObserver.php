<?php

namespace App\Observers;

use App\Events\AccountVerified;
use App\Models\Account;

class AccountObserver
{
    public function saved(Account $account)
    {
        \App\Helpers\Cache\Account::wipe($account);
    }

    public function updated(Account $account): void
    {
        if ($account->wasChanged('email_verified_at')
            && is_null($account->getOriginal('email_verified_at'))
            && ! is_null($account->email_verified_at)) {
            AccountVerified::dispatch($account);
        }
    }

    public function deleted(Account $account): void
    {
        \App\Helpers\Cache\Account::wipe($account);
    }
}
