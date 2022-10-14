<?php

namespace App\Jobs;

use App\Helpers\Cooperation\Tool\ToolHelper;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\User;
use App\Services\Models\NotificationService;
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
    public $withOldAdvices;

    public function __construct(User $user, InputSource $inputSource, Step $step, bool $withOldAdvices = true)
    {
        $this->user = $user;
        $this->inputSource = $inputSource;
        $this->step = $step;
        $this->withOldAdvices = $withOldAdvices;
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


    public function failed(\Exception $exception)
    {
        NotificationService::init()
            ->forBuilding($this->user->building)
            ->forInputSource($this->inputSource)
            ->setType(self::class)
            ->deactivate(force);
    }
}
