<?php

namespace App\Jobs;

use App\Mail\UnreadMessagesEmail;
use App\Models\Building;
use App\Models\Cooperation;
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
    protected $cooperation;
    protected $building;
    protected $notificationSetting;
    protected $unreadMessageCount;

    /**
     *
     *
     * @param  Cooperation  $cooperation
     * @param  User  $user
     * @param  Building  $building
     * @param  NotificationSetting  $notificationSetting
     * @param int $unreadMessageCount
     */
    public function __construct(Cooperation $cooperation, User $user, Building $building, NotificationSetting $notificationSetting, int $unreadMessageCount)
    {
        $this->notificationSetting = $notificationSetting;
        $this->user                = $user;
        $this->cooperation         = $cooperation;
        $this->building            = $building;
        $this->unreadMessageCount  = $unreadMessageCount;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->building instanceof Building) {

            // send the mail to the user
            \Mail::to($this->user->email)->send(new UnreadMessagesEmail($this->user, $this->cooperation, $this->unreadMessageCount));

            // after that has been done, update the last_notified_at to the current date
            $this->notificationSetting->last_notified_at = Carbon::now();
            $this->notificationSetting->save();

        } else {
            \Log::debug('it seems like user id '.$this->user->id.' has no building!');
        }
    }
}
