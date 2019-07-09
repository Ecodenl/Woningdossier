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

class SendNotifications extends Command {
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
		$notificationType = NotificationType::where( 'short',
			$this->option( 'type' ) )->first();

		// if it exist only send the specific notification type
		if ( $notificationType instanceof NotificationType ) {
			$this->info( 'Notification type: ' . $this->option( 'type' ) . ' exists, let\'s do some work.' );

			// get the cooperations with its users and buildings
            $cooperations = Cooperation::with( [ 'users.building'] )->withoutGlobalScopes()->get();

			foreach ( $cooperations as $cooperation ) {

				foreach ( $cooperation->users as $user ) {

					// same goes for the building
					$building = $user->building;

					// get their notification setting for the specific type.
					$notificationSetting = $user->notificationSettings()->where( 'type_id', $notificationType->id )->first();

					// if the notification setting, building and cooperation exists do some things.
					if ( $notificationSetting instanceof NotificationSetting && $building instanceof Building && $cooperation instanceof Cooperation ) {
						$now = Carbon::now();

						// check if the user has a last notified at
						if ( $notificationSetting->last_notified_at instanceof Carbon ) {

							$lastNotifiedAt = $notificationSetting->last_notified_at;
							$notifiedDiff   = $now->diff( $lastNotifiedAt );

							// get the total unread messages for a user within its given cooperation, after the last notified at. We dont want to spam users.
							$unreadMessageCount = PrivateMessageView::getTotalUnreadMessagesForUserAndCooperationAfterSpecificDate(
								$user,
								$cooperation,
								$lastNotifiedAt
							);

							// check if there actually are new messages
							if ( $unreadMessageCount > 0 ) {

								switch ( $notificationSetting->interval->short ) {
									case 'daily':
										// if the difference between now and the last notified date is 23 hours, send him a message
										if ( $this->almostMoreThanOneDayAgo( $notifiedDiff ) ) {
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
										if ( $this->almostMoreThanOneWeekAgo( $notifiedDiff ) ) {
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
							$notificationSetting->last_notified_at = Carbon::now()->subYear( 1 );
							$notificationSetting->save();
						}

					}

				}
			}
		} else {
			$this->info( 'Notification type: ' . $this->option( 'type' ) . ' was not provided or does not exist' );
		}

		$this->info( "Done" );
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
	 *
	 * @param \DateInterval $diff
	 *
	 * @return bool
	 */
	protected function almostMoreThanOneDayAgo( \DateInterval $diff )
	{
		if ( ! \App::environment( 'production' ) ) {
			return $diff->h >= 1 || $diff->days >= 1;
		}

		return ( $diff->h >= 23 && $diff->i >= 50 ) || $diff->days >= 1;
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
	 *
	 * @param \DateInterval $diff
	 *
	 * @return bool
	 */
	protected function almostMoreThanOneWeekAgo( \DateInterval $diff )
	{
		if ( ! \App::environment( 'production' ) ) {
			return $diff->h >= 4 || $diff->days >= 1;
		}

		return $diff->days >= 6 && $diff->h >= 23 && $diff->i >= 50;
	}
}
