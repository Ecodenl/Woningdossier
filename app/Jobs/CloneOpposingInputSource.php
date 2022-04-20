<?php

namespace App\Jobs;

use App\Models\Building;
use App\Models\InputSource;
use App\Services\CloneDataService;
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
    public InputSource $clonableInputSource;

    public function __construct(Building $building, InputSource $inputSource, InputSource $clonableInputSource)
    {
        $this->building = $building;
        $this->inputSource = $inputSource;
        $this->clonableInputSource = $clonableInputSource;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CloneDataService::init($this->building, $this->inputSource, $this->clonableInputSource)
            ->clone();
    }
}
