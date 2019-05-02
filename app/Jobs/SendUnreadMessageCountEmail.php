<?php

namespace App\Jobs;

use App\Mail\UnreadMessagesEmail;
use App\Models\PrivateMessageView;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendUnreadMessageCountEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $building;

    /**
     * SendUnreadMessageCountEmail constructor.
     *
     * @param  User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->building = $user->buildings()->first();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // get the unread message for a building id
        $unreadMessageCount = PrivateMessageView::getTotalUnreadMessagesCountByBuildingId($this->building->id);

        // send the mail to the user
        \Mail::to($this->user->email)
             ->send(new UnreadMessagesEmail($this->user, $unreadMessageCount));
    }
}
