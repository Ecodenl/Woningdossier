<?php

namespace App\Jobs;

use App\Helpers\Cooperation\Tool\ToolHelper;
use App\Jobs\Middleware\CheckLastResetAt;
use App\Helpers\Queue;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\User;
use App\Traits\Queue\HasNotifications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class RecalculateStepForUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, HasNotifications;

    public User $user;
    public InputSource $inputSource;
    public Step $step;
    public bool $withOldAdvices;

    public function __construct(User $user, InputSource $inputSource, Step $step, bool $withOldAdvices = true)
    {
        $this->queue = Queue::APP_HIGH;
        $this->user = $user;
        $this->inputSource = $inputSource;
        $this->step = $step;
        $this->withOldAdvices = $withOldAdvices;

        $this->setUuid();
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
            // if we dont want the old advices, turn it of.
            if (!$this->withOldAdvices) {
                $stepHelperClass = $stepHelperClass->withoutOldAdvices();
            }
            $stepHelperClass->createValues()->createAdvices();
        }
    }

    public function failed(Throwable $exception)
    {
        $this->deactivateNotification();

        report($exception);
    }

    public function middleware(): array
    {
        return [new CheckLastResetAt($this->user->building)];
    }
}
