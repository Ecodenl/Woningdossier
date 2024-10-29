<?php

namespace App\Services\Econobis\Payloads;

use App\Models\User;
use App\Services\BuildingCoachStatusService;
use App\Services\UserService;

class AppointmentDatePayload extends EconobisPayload
{
    public UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function buildPayload(): array
    {
        $building = $this->building;

        $mostRecentStatus = $building->getMostRecentBuildingStatus();
        $payload = ['appointment_date' => $mostRecentStatus->appointment_date?->toDateTimeString()];

        $connectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuildingId($building, true);

        foreach ($connectedCoaches as $connectedCoach) {
            $coachUser = $connectedCoach->coach;
            if ($this->userService->forUser($coachUser)->isRelatedWithEconobis()) {
                $payload['coaches'][] = [
                    'contact_id' => $coachUser->extra['contact_id'],
                    'user_id' => $coachUser->id,
                    'building_id' => $coachUser->building->id,
                    'account_id' => $coachUser->account_id,
                ];
            }
        }
        return $payload;
    }
}