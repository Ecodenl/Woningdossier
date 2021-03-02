<?php

namespace App\Jobs;

use App\Mail\UnreadMessagesEmail;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\PrivateMessage;
use App\Models\User;
use App\Services\PrivateMessageViewService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendUnreadMessageCountEmail implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $user;
    protected $cooperation;
    protected $building;
    protected $notificationSetting;
    protected $unreadMessageCount;

    public function __construct(Cooperation $cooperation, User $user, Building $building, NotificationSetting $notificationSetting, int $unreadMessageCount)
    {
        $this->notificationSetting = $notificationSetting;
        $this->user = $user;
        $this->cooperation = $cooperation;
        $this->building = $building;
        $this->unreadMessageCount = $unreadMessageCount;
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
            \Mail::to($this->user->account->email)->send(new UnreadMessagesEmail($this->user, $this->cooperation, $this->unreadMessageCount));

            // after that has been done, update the last_notified_at to the current date
            $this->notificationSetting->last_notified_at = Carbon::now();
            $this->notificationSetting->save();
        } else {
            \Log::debug('it seems like user id '.$this->user->id.' has no building!');
        }
    }

    public function failed(\Exception $exception)
    {
        // this functionality is here for people which mistyped they're email address
        // this will set the messages to read for the user in its resident its input source.
        // this way we prevent the mail for being sent over and over again.
        $messagesToSetRead = PrivateMessage::forMyCooperation()
            ->conversation($this->building->id);

        $messagesToSetRead = $messagesToSetRead->get();

        $inputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);
        PrivateMessageViewService::markAsReadByUser($messagesToSetRead, $this->user, $inputSource);
    }
}
