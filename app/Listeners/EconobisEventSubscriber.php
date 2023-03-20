<?php

namespace App\Listeners;

use App\Events\BuildingAppointmentDateUpdated;
use App\Events\BuildingStatusUpdated;
use App\Events\UserDeleted;
use App\Jobs\Econobis\Out\SendAppointmentDateToEconobis;
use App\Jobs\Econobis\Out\SendBuildingStatusToEconobis;
use App\Jobs\Econobis\Out\SendUserDeletedToEconobis;
use App\Services\BuildingCoachStatusService;
use App\Services\UserService;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Log;

class EconobisEventSubscriber
{
    public UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function sendAppointmentDateToEconobis(BuildingAppointmentDateUpdated $event)
    {
        $canSendUserInformationToEconobis = $event
            ->building
            ->user->account->can(
                'send-user-information-to-econobis',
                [$event->building->user]
            );
        $userHasConnectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuildingId($event->building->id)->isNotEmpty();
        if ($canSendUserInformationToEconobis && $userHasConnectedCoaches) {
            SendAppointmentDateToEconobis::dispatch($event->building);
        }
    }

    public function sendBuildingStatusToEconobis(BuildingStatusUpdated $event)
    {
        $canSendUserInformationToEconobis = $event
            ->building
            ->user->account->can(
                'send-user-information-to-econobis',
                [$event->building->user]
            );
        $userHasConnectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuildingId($event->building->id)->isNotEmpty();
        if ($canSendUserInformationToEconobis && $userHasConnectedCoaches) {
            SendBuildingStatusToEconobis::dispatch($event->building);
        }
    }

    public function sendUserDeletedToEconobis(UserDeleted $event)
    {
        // so this is the same as the policy used above, but at this stage the user does not exist anymore.
        // so we have to do it manually.
        if (!empty($event->accountRelated['account_id'])) {
            SendUserDeletedToEconobis::dispatch($event->accountRelated);
        }
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            BuildingAppointmentDateUpdated::class => 'sendAppointmentDateToEconobis',
            BuildingStatusUpdated::class => 'sendBuildingStatusToEconobis',
            UserDeleted::class => 'sendUserDeletedToEconobis'
        ];
    }
}
