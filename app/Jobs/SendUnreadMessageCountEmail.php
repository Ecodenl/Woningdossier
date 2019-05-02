<?php

namespace App\Jobs;

use App\Mail\UnreadMessagesEmail;
use App\Models\PrivateMessageView;
use App\Models\User;
use App\NotificationSetting;
use Carbon\Carbon;
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
    protected $notificationSetting;

    /**
     * SendUnreadMessageCountEmail constructor.
     *
     * @param  User  $user
     * @param  NotificationSetting  $notificationSetting
     */
    public function __construct(User $user, NotificationSetting $notificationSetting)
    {
        $this->notificationSetting = $notificationSetting;
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

        // after that has been done, update the last_notified_at to the current date
        $this->notificationSetting->last_notified_at = Carbon::now();
        $this->notificationSetting->save();
    }
}
