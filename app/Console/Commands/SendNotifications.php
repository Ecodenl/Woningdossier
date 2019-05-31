<?php

namespace App\Console\Commands;

use App\Jobs\SendUnreadMessageCountEmail;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\NotificationType;
use App\Models\PrivateMessageView;
use App\Models\User;
use App\NotificationSetting;
use Carbon\Carbon;
use Doctrine\DBAL\Schema\Schema;
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

            // get the cooperations with its users and buildings
            $cooperations = Cooperation::with(['users.buildings'])->has('users')->get();


            foreach ($cooperations as $cooperation) {

                foreach ($cooperation->users as $user) {

                    // same goes for the building
                    $building = $user->buildings->first();

                    // get their notification setting for the specific type.
                    $notificationSetting = $user->notificationSettings()->where('type_id', $notificationType->id)->first();

                    // if the notification setting, building and cooperation exists do some things.
                    if ($notificationSetting instanceof NotificationSetting && $building instanceof Building && $cooperation instanceof Cooperation) {
                        $now = Carbon::now();

                        // check if the user has a last notified at
                        if ($notificationSetting->last_notified_at instanceof Carbon) {

                            $lastNotifiedAt = $notificationSetting->last_notified_at;
                            $notifiedDiff   = $now->diff($lastNotifiedAt);

                            // get the total unread messages for a user within its given cooperation, after the last notified at. We dont want to spam users.
                            $unreadMessageCount = PrivateMessageView::getTotalUnreadMessagesForUserAndCooperationAfterSpecificDate(
                                $user, $cooperation, $lastNotifiedAt
                            );

                            // check if there actually are new messages
                            if ($unreadMessageCount > 0) {

                                switch ($notificationSetting->interval->short) {
                                    case 'daily':
                                        // if the difference between now and the last notified date is 23 hours, send him a message
                                        if (($notifiedDiff->h >= 23 && $notifiedDiff->i >= 50) || $notifiedDiff->days >= 1) {
                                            SendUnreadMessageCountEmail::dispatch(
                                                $cooperation, $user, $building, $notificationSetting,
                                                $unreadMessageCount
                                            );
                                        }
                                        break;
                                    case 'weekly':
                                        if ($now->diff($lastNotifiedAt)->days >= 6 && $notifiedDiff->h >= 23 && $notifiedDiff->i >= 50) {
                                            SendUnreadMessageCountEmail::dispatch(
                                                $cooperation, $user, $building, $notificationSetting,
                                                $unreadMessageCount
                                            );
                                        }
                                        break;
                                    case 'no-interest':
                                        break;
                                }
                            }
                        } else {
                            // the user has never been notified, so we set subtract one year from the current one.
                            $notificationSetting->last_notified_at = Carbon::now()->subYear(1);
                            $notificationSetting->save();
                        }

                    }

                }
            }
        } else {
            $this->info('Notification type: '.$this->option('type').' was not provided or does not exist');
        }

        $this->info("Done");
    }
}
