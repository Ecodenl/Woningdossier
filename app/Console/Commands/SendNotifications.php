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
        // get the current notification type
        $notificationType = NotificationType::where('short', $this->option('type'))->first();

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
        // select pmv.private_message_id, pmv.user_id, pmv.created_at, ns.last_notified_at
        // from private_message_views as pmv
        // left join notification_settings as ns on pmv.user_id = ns.user_id
        // where pmv.read_at is null and ns.interval_id in (1,2) and pmv.created_at > ns.last_notified_at
        return DB::table("private_message_views as pmv")
            // TODO: Unnecessary select?
            ->select(
                "pmv.private_message_id",
                "pmv.user_id",
                "pmv.created_at",
                "ns.last_notified_at"
            )
            ->leftJoin("notification_settings as ns", "pmv.user_id", "=", "ns.user_id")
            ->whereNull("pmv.read_at")
            ->whereIn("ns.interval_id", [1, 2])
            ->whereRaw("pmv.created_at > ns.last_notified_at")
            ->select(["pmv.user_id"])
            ->distinct()
            ->get();
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
