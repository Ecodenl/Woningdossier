<?php

namespace App\Jobs;

use App\Jobs\Middleware\CheckLastResetAt;
use App\Helpers\Queue;
use App\Models\Building;
use App\Models\InputSource;
use App\Services\Cloning\CloneDataService;
use App\Traits\Queue\HasNotifications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class CloneOpposingInputSource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasNotifications;

    public $queue = Queue::APP_HIGH;

    public Building $building;
    public InputSource $inputSource;
    public InputSource $cloneableInputSource;

    public function __construct(Building $building, InputSource $inputSource, InputSource $cloneableInputSource)
    {
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
    public function handle()
    {
        CloneDataService::init($this->building, $this->inputSource, $this->cloneableInputSource)
            ->clone();
    }

    public function failed(Throwable $exception)
    {
        $this->deactivateNotification();

        report($exception);
    }

    public function middleware(): array
    {
        return [new CheckLastResetAt($this->building)];
    }
}
