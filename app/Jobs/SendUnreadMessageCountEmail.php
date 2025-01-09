<?php

namespace App\Jobs;

use App\Helpers\Queue;
use App\Mail\UnreadMessagesEmail;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\NotificationSetting;
use App\Models\PrivateMessage;
use App\Models\User;
use App\Services\PrivateMessageViewService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendUnreadMessageCountEmail implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $cooperation;
    protected $building;
    protected $notificationSetting;
    protected $unreadMessageCount;

    /**
     * The number of seconds after which the job's unique lock will be released.
     *
     * @var int
     */
    public $uniqueFor = 3600;

    public function __construct(Cooperation $cooperation, User $user, Building $building, NotificationSetting $notificationSetting, int $unreadMessageCount)
    {
        $this->queue = Queue::APP_EXTERNAL;
        $this->notificationSetting = $notificationSetting;
        $this->user = $user;
        $this->cooperation = $cooperation;
        $this->building = $building;
        $this->unreadMessageCount = $unreadMessageCount;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->building instanceof Building) {
            // send the mail to the user
            Mail::to($this->user->account->email)
                ->send(new UnreadMessagesEmail($this->user, $this->cooperation, $this->unreadMessageCount));

            // after that has been done, update the last_notified_at to the current date
            $this->notificationSetting->last_notified_at = Carbon::now();
            $this->notificationSetting->save();
        } else {
            Log::debug('it seems like user id '.$this->user->id.' has no building!');
        }
    }

    public function failed(Throwable $exception)
    {
        Log::debug($exception->getMessage());
        // This functionality is here for people which mistyped their email address.
        // This will set the messages to read for the user in the resident's input source.
        // This way we prevent the mail from being sent over and over again.
        $messagesToSetRead = PrivateMessage::where('to_cooperation_id', $this->cooperation->id)
            ->conversation($this->building->id);

        $messagesToSetRead = $messagesToSetRead->get();

        $inputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);
        PrivateMessageViewService::markAsReadByUser($messagesToSetRead, $this->user, $inputSource);

        report($exception);
    }

    /**
     * Defines the uniqueness for the job. Only one job per user per cooperation.
     */
    public function uniqueId() : string
    {
        return __CLASS__ . '-' . $this->user->id.'-'.$this->cooperation->id;
    }
}
