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

    public function handleSendAppointmentDateToEconobis(BuildingAppointmentDateUpdated $event)
    {
        Log::debug(__METHOD__);
        $canSendUserInformationToEconobis = $this->canUserSendInformationToEconobis($event);
        $userHasConnectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuilding($event->building)->isNotEmpty();
        if ($canSendUserInformationToEconobis && $userHasConnectedCoaches) {
            Log::debug(__METHOD__ . ' - dispatching SendAppointmentDateToEconobis');
            SendAppointmentDateToEconobis::dispatch($event->building);
        }
    }

    public function handleSendBuildingStatusToEconobis(BuildingStatusUpdated $event)
    {
        // Econobis only wants the status if it's `executed` ("uitgevoerd")
        $econobisWantsStatus = ($status = $event->building->getMostRecentBuildingStatus()?->status) instanceof Status && $status->short === 'executed';
        $canSendUserInformationToEconobis = $this->canUserSendInformationToEconobis($event);
        $userHasConnectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuilding($event->building)->isNotEmpty();
        if ($canSendUserInformationToEconobis && $userHasConnectedCoaches && $econobisWantsStatus) {
            SendBuildingStatusToEconobis::dispatch($event->building);
        }
    }

    public function handleSendScanStatusToEconobis(BuildingCompletedHisFirstSubStep|UserResetHisBuilding $event)
    {
        if ($this->canUserSendInformationToEconobis($event)) {
            SendScanStatusToEconobis::dispatch($event->building);
        }
    }

    public function handleSendBuildingFilledInAnswersToEconobis(UserResetHisBuilding $event)
    {
        if ($this->canUserSendInformationToEconobis($event)) {
            SendBuildingFilledInAnswersToEconobis::dispatch($event->building);
        }
    }

    public function handleSendUserDeletedToEconobis(UserDeleted $event)
    {
        // The user no longer exists at this point and the cooperation may also have been removed,
        // so we work from the snapshot the event carries.
        if (! empty($event->context['account_id'])) {
            SendUserDeletedToEconobis::dispatch($event->cooperation, [
                'building_id' => $event->context['building_id'] ?? null,
                'user_id'     => $event->context['user_id']     ?? null,
                'account_id'  => $event->context['account_id'],
                'contact_id'  => $event->context['extra']['contact_id'] ?? null,
            ]);
        }
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            BuildingAppointmentDateUpdated::class => 'handleSendAppointmentDateToEconobis',
            BuildingStatusUpdated::class => 'handleSendBuildingStatusToEconobis',
            UserDeleted::class => 'handleSendUserDeletedToEconobis',
            BuildingCompletedHisFirstSubStep::class => 'handlSendScanStatusToEconobis',
            UserResetHisBuilding::class => ['handlSendScanStatusToEconobis', 'handleSendBuildingFilledInAnswersToEconobis'],
        ];
    }

    private function canUserSendInformationToEconobis($event)
    {
        // A building belongs to a single user, but might not be found if we don't scope it for all cooperations.
        $user = $event->building->user()->forAllCooperations()->first();
        return $user->account->can(
            'send-user-information-to-econobis',
            [$user]
        );
    }
}
