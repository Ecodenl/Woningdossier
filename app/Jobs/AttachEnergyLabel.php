<?php

namespace App\Jobs;

use App\Jobs\Middleware\CheckLastResetAt;
use App\Helpers\Queue;
use App\Models\Building;
use App\Services\EpOnline\EnergyLabelService;
use Throwable;

class AttachEnergyLabel extends NonHandleableJobAfterReset
{
    public Building $building;

    public $tries = 3;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Building $building)
    {
        parent::__construct();
        $this->building = $building;
        $this->queue = Queue::APP_EXTERNAL;
    }

    /**
     * Execute the job.
     */
    public function handle(EnergyLabelService $energyLabelService): void
    {
        $building = $this->building;

        $energyLabelService->forBuilding($building)
            ->syncEnergyLabel();
    }

    public function middleware(): array
    {
        return [new CheckLastResetAt($this->building)];
    }

    public function failed(Throwable $exception)
    {
        report($exception);
    }
}
