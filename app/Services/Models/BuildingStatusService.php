<?php

namespace App\Services\Models;

use App\Events\BuildingAppointmentDateUpdated;
use App\Events\BuildingStatusUpdated;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\Scan;
use App\Models\Status;
use App\Services\WoonplanService;
use App\Traits\FluentCaller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
