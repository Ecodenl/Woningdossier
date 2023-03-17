<?php

namespace App\Listeners;

use App\Events\BuildingAppointmentDateUpdated;
use App\Events\BuildingStatusUpdated;
use App\Jobs\Econobis\Out\SendAppointmentDateToEconobis;
use App\Jobs\Econobis\Out\SendBuildingStatusToEconobis;
use App\Models\BuildingStatus;
use App\Services\UserService;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Artisan;

class EconobisEventSubscriber
{
    public UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function sendAppointmentDateToEconobis(BuildingAppointmentDateUpdated $event)
    {
        if ($event->building->user->account->can('send-user-information-to-econobis', [$event->building->user])) {
            SendAppointmentDateToEconobis::dispatch($event->building);
        }
    }
    public function sendBuildingStatusToEconobis(BuildingStatusUpdated $event)
    {
        if ($event->building->user->account->can('send-user-information-to-econobis', [$event->building->user])) {
            SendBuildingStatusToEconobis::dispatch($event->building);
        }
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            BuildingAppointmentDateUpdated::class => 'sendAppointmentDateToEconobis',
            BuildingStatusUpdated::class => 'sendBuildingStatusToEconobis'
        ];
    }
}
