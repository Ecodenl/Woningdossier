<?php

namespace App\Events;

use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Queue\SerializesModels;

class Registered
{
    use SerializesModels;

    /**
     * The authenticated user.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    public $user;

    /**
     * The current cooperation.
     *
     * @var Cooperation
     */
    public $cooperation;

    /**
     * Registered constructor.
     */
    public function __construct(Cooperation $cooperation, User $user)
    {
        $this->cooperation = $cooperation;
        $this->user = $user;
    }
}
