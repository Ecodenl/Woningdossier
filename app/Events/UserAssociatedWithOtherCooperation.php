<?php

namespace App\Events;

use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserAssociatedWithOtherCooperation
{
    use Dispatchable;
    use InteractsWithSockets;
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
     *
     * @param Cooperation $cooperation
     * @param User        $user
     */
    public function __construct(Cooperation $cooperation, User $user)
    {
        $this->cooperation = $cooperation;
        $this->user = $user;
    }
}
