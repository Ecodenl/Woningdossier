<?php

namespace App\Services\Models;

use App\Events\BuildingStatusUpdated;
use App\Models\Building;
use App\Models\Status;

class BuildingStatusService
{
    public ?Building $building;

    public function forBuilding(Building $building): self
    {
        $this->building = $building;
        return $this;
    }


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
     *
     * @return void
     */
    public function setStatus($status)
    {
        $statusModel = $this->resolveStatusModel($status);

        $this->building->buildingStatuses()->create([
            'status_id' => $statusModel->id,
            'appointment_date' => $this->building->getAppointmentDate(),
        ]);

        BuildingStatusUpdated::dispatch($this->building);
    }
}
