<?php

namespace App\Jobs;

use App\Models\InputSource;
use App\Models\Step;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RecalculateStepForUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $inputSource;
    protected $step;

    public function __construct(User $user, InputSource $inputSource, Step $step)
    {
        $this->user = $user;
        $this->inputSource = $inputSource;
        $this->step = $step;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::debug("Recalculating step: ".$this->step->name);
        $stepClass = 'App\\Helpers\\Cooperation\Tool\\' . Str::singular(Str::studly($this->step->short)) . 'Helper';
        $stepHelperClass = new $stepClass($this->user, $this->inputSource);
        $stepHelperClass->createValues()->createAdvices();
    }
}
