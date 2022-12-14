<?php

namespace App\Console\Commands\Upgrade\LiteScan;

use App\Models\CompletedSubStep;
use App\Models\InputSource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TriggerSaveForAllCompletedSubSteps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:lite-scan:trigger-save-for-all-completed-sub-steps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update cooperation measures.';

    public function handle()
    {
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);

        CompletedSubStep::allInputSources()->where('input_source_id', '!=', $masterInputSource->id)
            ->orderBy('id')->chunkById(100, function ($css) {
                foreach ($css as $step) {
                    $date = $step->updated_at ?? now();
                    $step->updated_at = $date->addSecond();
                    $step->save();

                    // Doing this will inevitably fill the queue... We sleep to allow the database to breathe.
                    // Who at PHP Corp decided to NOT create any sleep function that supports milliseconds!?
                    usleep(500000);
                }
        });
    }
}