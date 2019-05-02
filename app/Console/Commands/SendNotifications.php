<?php

namespace App\Console\Commands;

use App\Jobs\SendUnreadMessageCountEmail;
use App\Models\NotificationType;
use App\Models\User;
use App\NotificationSetting;
use Illuminate\Console\Command;
use Illuminate\Queue\Jobs\Job;

class SendNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notifications {type : Notification type to be send, if left empty all notification types will be sent.}';

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
        $notificationType = NotificationType::where('short', $this->argument('type'))->first();

        if ($notificationType instanceof NotificationType) {
            $this->line('Notification type: '.$this->argument('type').' exists, lets do some work.');
            $users = User::all();

            foreach ($users as $user) {
                $notificationSetting = $user->notificationSettings()->where('type_id', $notificationType->id)->first();


                if ($notificationSetting instanceof NotificationSetting && is_null($notificationSetting->last_notified_at)) {
                    SendUnreadMessageCountEmail::dispatch($user);
                }
            }

        } else {
            $this->line('Notification type: '.$this->argument('type').' does not exist');
        }

    }
}
