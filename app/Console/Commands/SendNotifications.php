<?php

namespace App\Console\Commands;

use App\Jobs\SendUnreadMessageCountEmail;
use App\Models\NotificationType;
use App\Models\User;
use App\NotificationSetting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Queue\Jobs\Job;

class SendNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notifications {--type= : Notification type to be send, if left empty all notification types will be sent.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the user notifications based on their interval and last_send_on';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // get the current notification type
        $notificationType = NotificationType::where('short', $this->option('type'))->first();

        // if it exist only send the specific notification type
        if ($notificationType instanceof NotificationType) {
            $this->line('Notification type: '.$this->option('type').' exists, lets do some work.');
            // get all the users
            $users = User::all();

            // loop through all the users
            foreach ($users as $user) {

                // get their notification setting for the specific type.
                $notificationSetting = $user->notificationSettings()->where('type_id', $notificationType->id)->first();

                // if the notification setting exists do some stuff
                if ($notificationSetting instanceof NotificationSetting) {

                    // check if the user has ever been notfied, if not we will set the last_notified_at to now.
                    if (is_null($notificationSetting->last_notified_at)) {
                        $notificationSetting->last_notified_at = Carbon::now();
                        $notificationSetting->save();
                    } else {
                        $lastNotifiedAt = $notificationSetting->last_notified_at;

                        switch ($notificationSetting->interval->short) {
                            case 'daily':
                                $today = Carbon::now();
                                // if the difference between now and the last notified date is 23 hours, send him a message
                                if ($today->diff($lastNotifiedAt)->h >= 23) {
                                    SendUnreadMessageCountEmail::dispatch($user);
                                }
                        }
                    }

                }

            }

        } else {
            $this->line('Notification type: '.$this->option('type').' was not provided or does not exist');
        }

    }
}
