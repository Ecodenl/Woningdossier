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
    protected $description = 'Send the user notifications based on their interval and last_notified_at';

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
            $this->info('Notification type: '.$this->option('type').' exists, let\'s do some work.');
            // get all the users
            $users = User::all();

            $bar = $this->output->createProgressBar(count($users));

            $bar->start();

            // loop through all the users
            foreach ($users as $user) {

                // get their notification setting for the specific type.
                $notificationSetting = $user->notificationSettings()->where('type_id', $notificationType->id)->first();

                // if the notification setting exists do some stuff
                if ($notificationSetting instanceof NotificationSetting) {
                    $bar->advance();
                    $now = Carbon::now();

                    // if its null, set it to now.
                    if (is_null($notificationSetting->last_notified_at)) {
                        SendUnreadMessageCountEmail::dispatch($user, $notificationSetting);
                    }


                    // check when the user has been notified for the last time, and notify them again if needed.
                    if ($notificationSetting->last_notified_at instanceof Carbon) {

                        $lastNotifiedAt = $notificationSetting->last_notified_at;
                        $notifiedDiff = $now->diff($lastNotifiedAt);

                        switch ($notificationSetting->interval->short) {
                            case 'daily':
                                // if the difference between now and the last notified date is 23 hours, send him a message
                                if ($notifiedDiff->h >= 23 && $notifiedDiff->i >= 50) {
                                    SendUnreadMessageCountEmail::dispatch($user, $notificationSetting);
                                }
                                break;
                            case 'weekly':
                                if ($now->diff($lastNotifiedAt)->days >= 6 && $notifiedDiff->h >= 23 && $notifiedDiff->i >= 50) {
                                    SendUnreadMessageCountEmail::dispatch($user, $notificationSetting);
                                }
                                break;
                        }
                    }

                }

            }

        } else {
            $this->info('Notification type: '.$this->option('type').' was not provided or does not exist');
        }

        $bar->finish();
        $this->info("\n Done");
    }
}
