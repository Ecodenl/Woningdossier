<?php

namespace App\Events;

use App\Models\Cooperation;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class UserAssociatedWithOtherCooperation
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The authenticated user.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    public $user;

    /**
     * The current cooperation
     *
     * @var Cooperation
     */
    public $cooperation;


    /**
     * Registered constructor.
     *
     * @param  Cooperation  $cooperation
     * @param  User         $user
     */
    public function __construct(Cooperation $cooperation, User $user)
    {
        $this->cooperation = $cooperation;
        $this->user = $user;
    }
}
