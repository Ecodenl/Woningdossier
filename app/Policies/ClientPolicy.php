<?php

namespace App\Policies;

use App\Models\Account;
use App\Models\Client;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    public function delete(Account $account, Client $client): bool
    {
        return $client->tokens()->count() === 0;
    }
}
