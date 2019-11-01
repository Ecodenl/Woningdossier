<?php

namespace App\Observers;

use App\Models\Account;

class AccountObserver {

    public function updated(Account $account){
        \App\Helpers\Cache\Account::wipe($account->id);
    }
}