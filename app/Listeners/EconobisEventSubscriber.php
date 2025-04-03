<?php

namespace App\Listeners;

use App\Events\BuildingAppointmentDateUpdated;
use App\Events\BuildingCompletedHisFirstSubStep;
use App\Events\BuildingStatusUpdated;
use App\Events\UserDeleted;
use App\Events\UserResetHisBuilding;
use App\Jobs\Econobis\Out\SendAppointmentDateToEconobis;
use App\Jobs\Econobis\Out\SendBuildingFilledInAnswersToEconobis;
use App\Jobs\Econobis\Out\SendBuildingStatusToEconobis;
use App\Jobs\Econobis\Out\SendScanStatusToEconobis;
use App\Jobs\Econobis\Out\SendUserDeletedToEconobis;
use App\Models\Status;
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
        Log::debug(__METHOD__);
        $canSendUserInformationToEconobis = $this->canUserSendInformationToEconobis($event);
        $userHasConnectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuildingId($event->building->id)->isNotEmpty();
        if ($canSendUserInformationToEconobis && $userHasConnectedCoaches) {
            Log::debug(__METHOD__ . ' - dispatching SendAppointmentDateToEconobis');
            SendAppointmentDateToEconobis::dispatch($event->building);
        }
    }

    public function sendBuildingStatusToEconobis(BuildingStatusUpdated $event)
    {
        // Econobis only wants the status if it's `executed` ("uitgevoerd")
        $econobisWantsStatus = ($status = optional($event->building->getMostRecentBuildingStatus())->status) instanceof Status && $status->short === 'executed';
        $canSendUserInformationToEconobis = $this->canUserSendInformationToEconobis($event);
        $userHasConnectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuildingId($event->building->id)->isNotEmpty();
        if ($canSendUserInformationToEconobis && $userHasConnectedCoaches && $econobisWantsStatus) {
            SendBuildingStatusToEconobis::dispatch($event->building);
        }
    }

    public function sendScanStatusToEconobis($event)
    {
        if ($this->canUserSendInformationToEconobis($event)) {
            SendScanStatusToEconobis::dispatch($event->building);
        }
    }

    public function sendBuildingFilledInAnswersToEconobis($event)
    {
        if ($this->canUserSendInformationToEconobis($event)) {
            SendBuildingFilledInAnswersToEconobis::dispatch($event->building);
        }
    }

    public function sendUserDeletedToEconobis(UserDeleted $event)
    {
        // so this is the same as the policy used above, but at this stage the user does not exist anymore.
        // so we have to do it manually.
        if (! empty($event->accountRelated['account_id'])) {
            SendUserDeletedToEconobis::dispatch($event->cooperation, $event->accountRelated);
        }
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            BuildingAppointmentDateUpdated::class => 'sendAppointmentDateToEconobis',
            BuildingStatusUpdated::class => 'sendBuildingStatusToEconobis',
            UserDeleted::class => 'sendUserDeletedToEconobis',
            BuildingCompletedHisFirstSubStep::class => 'sendScanStatusToEconobis',
            UserResetHisBuilding::class => ['sendScanStatusToEconobis', 'sendBuildingFilledInAnswersToEconobis'],
        ];
    }

    private function canUserSendInformationToEconobis($event)
    {
        $user = $event->building->user()->forAllCooperations()->first();
        return $user->account->can(
            'send-user-information-to-econobis',
            [$user]
        );
    }
}
