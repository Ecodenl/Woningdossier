<?php

namespace App\Console\Commands\Tool;

use App\Helpers\Queue;
use App\Jobs\ProcessRecalculate;
use App\Jobs\RecalculateStepForUser;
use App\Models\Building;
use App\Models\CompletedStep;
use App\Models\Cooperation;
use App\Models\ExampleBuilding;
use App\Models\InputSource;
use App\Models\Notification;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\ExampleBuildingService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class RecalculateForUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tool:recalculate 
                                            {--user=* : The ID\'s of the users }
                                            {--input-source=* : Input source shorts, will only use the given input sources. When left empty all input sources will be used.} 
                                            {--cooperation= : Cooperation ID, use full to recalculate all users for a specific cooperation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate the tool for a given user, will create new user action plan advices.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! is_null($this->option('cooperation'))) {
            if (! Cooperation::find($this->option('cooperation'))) {
                $this->error('Cooperation not found!');

                return;
            }
            $users = User::forMyCooperation($this->option('cooperation'))->with('building')->get();
        } else {
            $users = User::findMany($this->option('user'))->load('building');
        }

        $bar = $this->output->createProgressBar($users->count());

        $bar->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%%");
        $bar->setMessage('Queuing up the recalculate..');


        $inputSourcesToRecalculate = [ InputSource::RESIDENT_SHORT ];

        if (! empty($this->option('input-source'))) {
            $inputSourcesToRecalculate = $this->option('input-source');
        }

        $inputSources = InputSource::whereIn('short', $inputSourcesToRecalculate)->get();

        Log::debug("tool:recalculate");
        /** @var User $user */
        foreach ($users as $user) {
            $bar->advance(1);

            foreach ($inputSources as $inputSource) {

                $completedSteps = $user->building
                    ->completedSteps()
                    ->forInputSource($inputSource)
                    ->whereHas('step', function ($query) {
                        $query
                            ->whereNotIn('steps.short', ['general-data', 'heat-pump', 'building-data',
                                'usage-quick-scan', 'living-requirements', 'residential-status'])
                            ->whereNull('parent_id');
                    })
                    ->get();

                if ($completedSteps->isNotEmpty()) {
                    Log::debug("Notification turned on for | b_id: {$user->building->id} | input_source_id: {$inputSource->id}");
                    Notification::setActive($user->building, $inputSource, true);
                } else {
                    Log::debug("No completed steps, no notification for | b_id: {$user->building->id} | input_source_id: {$inputSource->id}");
                }

                $stepsToRecalculateChain = [];

                /** @var CompletedStep $completedStep */
                foreach ($completedSteps as $completedStep) {
                    // user is interested, so recreate the advices for each step
                    $stepsToRecalculateChain[] = (new RecalculateStepForUser($user, $inputSource, $completedStep->step))
                        ->onQueue(Queue::ASYNC);
                }

                if (! empty($stepsToRecalculateChain)) {
                    Log::debug("Dispatching recalculate chain for | b_id: {$user->building->id} | input_source_id: {$inputSource->id}");
                    ProcessRecalculate::withChain($stepsToRecalculateChain)
                        ->dispatch()
                        ->onQueue(Queue::ASYNC);
                }
            }
        }
        $bar->finish();
    }

    private function isFirstTimeToolIsFilled(Building $building)
    {
        return true;

//        $inputSource      = InputSource::findByShort(InputSource::MASTER_SHORT);
//        $cookTypeQuestion = ToolQuestion::findByShort('cook-type');
//
//        return is_null($building->getAnswer($inputSource, $cookTypeQuestion));
    }
}
