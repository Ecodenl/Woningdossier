<?php

namespace App\Events;

use App\Models\Account;
use App\Models\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Facades\Log;

class UserChangedHisEmailEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cooperation;
    public $oldEmail;
    public $newEmail;
    public $user;
    public $account;

    /**
     * UserChangedHisEmailEvent constructor.
     *
     * @param  User  $user
     * @param  Account  $account
     * @param $oldEmail
     * @param $newEmail
     */
    public function __construct(User $user, Account $account, $oldEmail, $newEmail)
    {
        Log::debug('User changed his mail from '.$oldEmail.' to '.$newEmail);
        $this->user = $user;
        $this->account = $account;
        $this->oldEmail = $oldEmail;
        $this->newEmail = $newEmail;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
