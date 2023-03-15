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
        $payload = ['appointment_date' => $mostRecentStatus->appointment_date];

        $connectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuildingId($building->id);

        foreach ($connectedCoaches as $connectedCoach) {
            $coachUser = User::find($connectedCoach->coach_id);
            if ($this->userService->forUser($coachUser)->isRelatedWithEconobis()) {
                $payload['coaches'][] = [
                    'contact_id' => $coachUser->extra['contact_id']
                ];
            }
        }

        return $payload;
    }
}