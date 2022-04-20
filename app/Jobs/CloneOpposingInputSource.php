<?php

namespace App\Jobs;

use App\Models\InputSource;
use App\Models\User;
use App\Services\CloneDataService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CloneOpposingInputSource implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;
    public InputSource $inputSource;
    public InputSource $clonableInputSource;

    public function __construct(User $user, InputSource $inputSource, InputSource $clonableInputSource)
    {
        $this->user = $user;
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
        CloneDataService::init($this->user, $this->inputSource, $this->clonableInputSource)
            ->clone();
    }
}
