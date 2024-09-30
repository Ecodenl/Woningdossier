<?php

namespace App\Jobs;

use App\Jobs\Middleware\CheckLastResetAt;
use App\Helpers\Queue;
use App\Models\Building;
use App\Models\InputSource;
use App\Services\Cloning\CloneDataService;
use App\Traits\Queue\HasNotifications;
use Throwable;

class CloneOpposingInputSource extends NonHandleableJobAfterReset
{
    use HasNotifications;

    public Building $building;
    public InputSource $inputSource;
    public InputSource $cloneableInputSource;

    public function __construct(Building $building, InputSource $inputSource, InputSource $cloneableInputSource)
    {
        parent::__construct();
        $this->queue = Queue::APP_HIGH;
        $this->building = $building;
        $this->inputSource = $inputSource;
        $this->cloneableInputSource = $cloneableInputSource;

        $this->setUuid();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        CloneDataService::init($this->building, $this->inputSource, $this->cloneableInputSource)
            ->clone();
    }

    public function failed(Throwable $exception)
    {
        $this->deactivateNotification();

        report($exception);
    }
}
