<?php

namespace App\Console\Commands;

use App\Jobs\SendUnreadMessageCountEmail;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\NotificationSetting;
use App\Models\NotificationType;
use App\Models\PrivateMessageView;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:notifications
                            {--type= : Notification type to send, if left empty all notification types will be sent.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send the user notifications based on their interval and last_notified_at';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        // We literally have ONE notification type. Let's not force devs to use a short for something that's not needed.
        $notificationType = empty($this->option('type'))
            ? NotificationType::first()
            : NotificationType::where('short', $this->option('type'))->first();

        // if it exists: only send the specific notification type
        if ($notificationType instanceof NotificationType) {
            $this->info('Notification type: ' . $this->option('type') . ' exists, let\'s do some work.');

            $userIdsData = $this->getUserIdsToNotify();

            foreach ($userIdsData as $userIdData) {
                $user = User::with(['cooperation', 'building'])->withoutGlobalScopes()->find($userIdData->user_id);
                if (! $user instanceof User) {
                    continue;
                }
                // same goes for the building
                $building = $user->building;
                $cooperation = $user->cooperation;

                // get their notification setting for the specific type.
                $notificationSetting = $user->notificationSettings()->where('type_id', $notificationType->id)->first();

                // if the notification setting, building and cooperation exists do some things.
                if ($notificationSetting instanceof NotificationSetting
                    && $building instanceof Building
                    && $cooperation instanceof Cooperation
                ) {
                    $now = Carbon::now();

                    // check if the user has a last notified at
                    if ($notificationSetting->last_notified_at instanceof Carbon) {
                        $lastNotifiedAt = $notificationSetting->last_notified_at;
                        $notifiedDiff = $now->diff($lastNotifiedAt);

                        // Get the total unread messages for a user within its given cooperation, after the
                        // last notified at. We dont want to spam users.
                        $unreadMessageCount = PrivateMessageView::getTotalUnreadMessagesForUserAndCooperationAfterSpecificDate(
                            $user,
                            $cooperation,
                            $lastNotifiedAt
                        );

                        // check if there actually are new messages
                        if ($unreadMessageCount > 0) {
                            switch ($notificationSetting->interval->short) {
                                case 'daily':
                                    // If the difference between now and the last notified
                                    // date is 23 hours, send them a message
                                    if ($this->almostMoreThanOneDayAgo($notifiedDiff)) {
                                        Log::debug(
                                            "Send daily mail to c " . $cooperation->id
                                            . ", u " . $user->id . ", b " . $building->id
                                            . ", unread " . $unreadMessageCount
                                        );
                                        SendUnreadMessageCountEmail::dispatch(
                                            $cooperation,
                                            $user,
                                            $building,
                                            $notificationSetting,
                                            $unreadMessageCount
                                        );
                                    }
                                    break;
                                case 'weekly':
                                    if ($this->almostMoreThanOneWeekAgo($notifiedDiff)) {
                                        Log::debug(
                                            "Send weekly mail to c " . $cooperation->id
                                            . ", u " . $user->id . ", b " . $building->id .
                                            ", unread " . $unreadMessageCount
                                        );
                                        SendUnreadMessageCountEmail::dispatch(
                                            $cooperation,
                                            $user,
                                            $building,
                                            $notificationSetting,
                                            $unreadMessageCount
                                        );
                                    }
                                    break;
                                case 'no-interest':
                                    // don't send anything
                                    break;
                            }
                        }
                    } else {
                        // the user has never been notified, so we set subtract one year from the current one.
                        $notificationSetting->update(['last_notified_at' => Carbon::now()->subYear()]);
                    }
                }
            }
        } else {
            $this->info('Notification type: ' . $this->option('type') . ' was not provided or does not exist');
        }

        $this->info('Done');

        return self::SUCCESS;
    }

    protected function getUserIdsToNotify(): Collection
    {
        // TODO: Should we fetch IDs for interval IDs beforehand instead of hardcoded?

        // SELECT DISTINCT(pmv.user_id) FROM private_message_views as pmv
        // LEFT JOIN notification_settings AS ns ON pmv.user_id = ns.user_id
        // WHERE pmv.read_at IS NULL
        // AND ns.interval_id IN (1,2)
        // AND pmv.created_at > ns.last_notified_at;

        // This query fetches all the users who have a private message unread.
        //$query = DB::table("private_message_views as pmv")
        return DB::table("private_message_views as pmv")
            ->leftJoin("notification_settings as ns", "pmv.user_id", "=", "ns.user_id")
            ->whereNull("pmv.read_at")
            ->whereIn("ns.interval_id", [1, 2])
            ->whereColumn('pmv.created_at', '>', 'ns.last_notified_at')
            ->select(["pmv.user_id"])
            ->distinct()
            ->get();

        //TODO @pvkouteren
        // Either we union query on below to fetch the cooperation user IDs (and would still require later filtering
        // so coaches don't get notifications for buildings they cannot access! (See PrivateMessageView line 131)),
        // or we alter the private message view table to a) drop the to_cooperation_id, or b) to always set the
        // user_id even if targeted at a cooperation, but with extra logic to set all views as read if one person
        // in the cooperation views it.
        // The issue is basically that we don't want everyone in a cooperation to handle the same message, but
        // the querying is rather complex now.

        //return DB::table("private_message_views as pmv")
        //    ->leftJoin('users', 'users.cooperation_id', '=', 'pmv.to_cooperation_id')
        //    ->leftJoin(
        //        'model_has_roles',
        //        fn (JoinClause $join) => $join
        //            ->on('users.id', '=', 'model_has_roles.model_id')
        //            ->where('model_has_roles.model_type', User::class)
        //    )
        //    ->leftJoin("notification_settings as ns", "users.id", "=", "ns.user_id")
        //    ->whereNull("pmv.read_at")
        //    ->whereIn("ns.interval_id", [1, 2])
        //    ->whereIn('role_id', [3,4,6])
        //    ->whereColumn('pmv.created_at', '>', 'ns.last_notified_at')
        //    ->select(['users.id'])
        //    ->distinct()
        //    ->union($query)
        //    ->get();

    }

    /**
     * Returns if a difference is almost one day ago. We allow for a little
     * variance because of speed variances which might mean that the previous
     * last_notified_at could be set at 24h - a couple of seconds (or minutes)
     * and would then not be triggered.
     * The less the command is run, the more important this variance.
     *
     * On local / test environments the diff for one day is set to one hour
     * (Hoom logic)
     */
    protected function almostMoreThanOneDayAgo(\DateInterval $diff): bool
    {
        if (! \App::environment('production')) {
            return $diff->h >= 1 || $diff->days >= 1;
        }

        return ($diff->h >= 23 && $diff->i >= 50) || $diff->days >= 1;
    }

    /**
     * Returns if a difference is almost one week ago. We allow for a little
     * variance because of speed variances which might mean that the previous
     * last_notified_at could be set at 1w - a couple of seconds (or minutes)
     * and would then not be triggered.
     * The less the command is run, the more important this variance.
     *
     * On local / test environments the diff for one week is set to 4 hours
     * (Hoom logic)
     */
    protected function almostMoreThanOneWeekAgo(\DateInterval $diff): bool
    {
        if (! \App::environment('production')) {
            return $diff->h >= 4 || $diff->days >= 1;
        }

        return ($diff->days >= 6 && $diff->h >= 23 && $diff->i >= 50) || $diff->days >= 7;
    }
}
