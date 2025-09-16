<?php

namespace App\Events;

use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAssociatedWithOtherCooperation
{
    use Dispatchable;
    use SerializesModels;

    /**
     * Registered constructor.
     */
    public function __construct(public Cooperation $cooperation, public User $user)
    {
        //
    }
}
