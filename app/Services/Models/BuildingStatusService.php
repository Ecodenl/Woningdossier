<?php

namespace App\Services\Models;

use App\Events\BuildingStatusUpdated;
use App\Models\Status;
use App\Traits\Services\HasBuilding;

class BuildingStatusService
{
    use HasBuilding;

    private function resolveStatusModel($status)
    {
        $statusModel = null;

        if (is_string($status)) {
            $statusModel = Status::where('short', $status)->first();
        }

        if ($status instanceof Status) {
            $statusModel = $status;
        }

        return $statusModel;
    }

    /**
     * convenient way of setting a status on a building.
     *
     * @param  string|Status  $status
     */
    public function setStatus($status): void
    {
        $statusModel = $this->resolveStatusModel($status);

        $this->building->buildingStatuses()->create([
            'status_id' => $statusModel->id,
            'appointment_date' => $this->building->getAppointmentDate(),
        ]);

        BuildingStatusUpdated::dispatch($this->building);
    }
}
