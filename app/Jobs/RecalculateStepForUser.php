<?php

namespace App\Jobs;

use App\Helpers\Cooperation\Tool\ToolHelper;
use App\Models\InputSource;
use App\Models\Notification;
use App\Models\Step;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RecalculateStepForUser implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public $user;
    public $inputSource;
    public $step;

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
        Log::debug('Recalculating step: '.$this->step->name);
        $stepClass = 'App\\Helpers\\Cooperation\Tool\\'.Str::singular(Str::studly($this->step->short)).'Helper';

        // Some steps don't have tool helpers. Let's check if it exists first
        if (class_exists($stepClass)) {
            /** @var ToolHelper $stepHelperClass */
            $stepHelperClass = new $stepClass($this->user, $this->inputSource);
            $stepHelperClass->createValues()->createAdvices();
        }
    }


    public function failed(\Exception $exception)
    {
        Notification::setActive($this->user->building, $this->inputSource, false);
    }
}
