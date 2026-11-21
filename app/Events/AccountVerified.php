<?php

namespace App\Events;

use App\Models\Account;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccountVerified
{
    use Dispatchable, SerializesModels;

    public function __construct(public Account $account)
    {
    }
}
