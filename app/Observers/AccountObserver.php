<?php

namespace App\Observers;

use App\Events\AccountVerified;
use App\Jobs\SmartTwin\Out\DeleteAccount;
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
        // The SmartTwin user hangs off the account, so it goes when the account goes — and only then.
        // Hooking the observer rather than UserService::deleteUser covers every deletion path, and the
        // restrict foreign key on users.account_id guarantees the last user is already gone by now.
        $smartTwinUserId = $account->smartTwinUserId();
        if (! empty($smartTwinUserId)) {
            DeleteAccount::dispatch($smartTwinUserId);
        }

        \App\Helpers\Cache\Account::wipe($account);
    }
}
