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
use Carbon\CarbonInterval;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
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
                            {--type=private-message : Notification type to send, if left empty all notification types will be sent.}';

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
                if ($notificationSetting instanceof NotificationSetting && $building instanceof Building && $cooperation instanceof Cooperation) {

                    // check if the user has a last notified at
                    if ($notificationSetting->last_notified_at instanceof Carbon) {
                        $lastNotifiedAt = $notificationSetting->last_notified_at;
                        $notifiedDiff = $lastNotifiedAt->diff(now());

                        // Get the total unread messages for a user within its given cooperation, after the
                        // last notified at. We dont want to spam users.
                        $unreadMessageCount = PrivateMessageView::getTotalUnreadMessagesForUserAndCooperationAfterSpecificDate(
                            $user,
                            $cooperation,
                            $lastNotifiedAt
                        );

                        // check if there actually are new messages
                        if ($unreadMessageCount > 0) {
                            $send = false;

                            switch ($notificationSetting->interval->short) {
                                case 'direct':
                                    if ($this->moreThanFiveMinutesAgo($notifiedDiff)) {
                                        Log::debug(
                                            sprintf(
                                                'Send direct mail to c %s, u %s, b %s, unread %s',
                                                $cooperation->getKey(),
                                                $user->getKey(),
                                                $building->getKey(),
                                                $unreadMessageCount
                                            )
                                        );
                                        $send = true;
                                    }
                                    break;
                                case 'daily':
                                    // If the difference between now and the last notified
                                    // date is 23 hours, send them a message
                                    if ($this->almostMoreThanOneDayAgo($notifiedDiff)) {
                                        Log::debug(
                                            sprintf(
                                                'Send daily mail to c %s, u %s, b %s, unread %s',
                                                $cooperation->getKey(),
                                                $user->getKey(),
                                                $building->getKey(),
                                                $unreadMessageCount
                                            )
                                        );
                                        $send = true;
                                    }
                                    break;
                                case 'weekly':
                                    if ($this->almostMoreThanOneWeekAgo($notifiedDiff)) {
                                        Log::debug(
                                            sprintf(
                                                'Send weekly mail to c %s, u %s, b %s, unread %s',
                                                $cooperation->getKey(),
                                                $user->getKey(),
                                                $building->getKey(),
                                                $unreadMessageCount
                                            )
                                        );
                                        $send = true;
                                    }
                                    break;
                                case 'no-interest':
                                    // don't send anything
                                    break;
                            }
                            if ($send) {
                                SendUnreadMessageCountEmail::dispatch($cooperation, $user, $building, $notificationSetting, $unreadMessageCount);
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

    /*
       select pmv.private_message_id, pmv.user_id, pmv.created_at, ns.last_notified_at
       from private_message_views as pmv
       left join notification_settings as ns on pmv.user_id = ns.user_id
       where pmv.read_at is null and ns.interval_id not in (3) and pmv.created_at > ns.last_notified_at
    */
    protected function getUserIdsToNotify(): Collection
    {
        return PrivateMessageView::query()
            ->select('private_message_views.user_id')
            ->distinct()
            ->join('notification_settings as ns', 'private_message_views.user_id', '=', 'ns.user_id')
            ->whereNull('read_at')
            ->whereNotIn('ns.interval_id', [3])
            ->whereColumn('private_message_views.created_at', '>', 'ns.last_notified_at')
            ->pluck('user_id');
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
    protected function almostMoreThanOneDayAgo(CarbonInterval $diff): bool
    {
        if (! App::environment('production')) {
            return $diff->totalHours >= 1;
        }

        return $diff->totalMinutes >= (23 * 60 + 50); // 1430 minuten
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
    protected function almostMoreThanOneWeekAgo(CarbonInterval $diff): bool
    {
        if (! App::environment('production')) {
            return $diff->totalHours >= 4;
        }

        return $diff->totalMinutes >= (6 * 24 * 60 + 23 * 60 + 50); // 10070 minuten
    }

    protected function moreThanFiveMinutesAgo(CarbonInterval $diff): bool
    {
        // no difference with live
        return $diff->totalMinutes > 5;
    }
}
