<?php

namespace App\Events;

use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Queue\SerializesModels;

class Registered
{
    use SerializesModels;

    /**
     * Registered constructor.
     */
    public function __construct(public Cooperation $cooperation, public User $user)
    {
        //
    }
}
