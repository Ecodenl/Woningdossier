<?php

namespace App\Jobs;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\Notification;
use App\Services\Cloning\CloneDataService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CloneOpposingInputSource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Building $building;
    public InputSource $inputSource;
    public InputSource $cloneableInputSource;

    public function __construct(Building $building, InputSource $inputSource, InputSource $cloneableInputSource)
    {
        $this->building = $building;
        $this->inputSource = $inputSource;
        $this->cloneableInputSource = $cloneableInputSource;
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

    public function failed(\Exception $exception)
    {
        Notification::setActive($this->building, $this->inputSource, CloneOpposingInputSource::class, false);
    }
}
