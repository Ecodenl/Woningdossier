<?php

namespace App\Console\Commands\Tool;

use App\Helpers\StepHelper;
use App\Jobs\ProcessRecalculate;
use App\Jobs\RecalculateStepForUser;
use App\Models\CompletedStep;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Input;

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
        if (!is_null($this->option('cooperation'))) {
            if (!Cooperation::find($this->option('cooperation'))) {
                $this->error('Cooperation not found!');
                return;
            }
            $users = User::forMyCooperation($this->option('cooperation'))->with('building')->get();
        } else {
            $users = User::findMany($this->option('user'))->load('building');
        }

        $bar = $this->output->createProgressBar($users->count());

        $bar->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%%");
        $bar->setMessage("Queuing up the recalculate..");

        // default
        $inputSourcesToRecalculate = ['resident', 'coach'];

        if (!empty($this->option('input-source'))) {
            $inputSourcesToRecalculate = $this->option('input-source');
        }

        $inputSources = InputSource::whereIn('short', $inputSourcesToRecalculate)
            ->pluck('id')
            ->toArray();

        /** @var User $user */
        foreach ($users as $user) {
            $bar->advance(1);

            // get the completed steps for a user.
            $completedSteps = $user->building
                ->completedSteps()
                ->whereHas('step', function (Builder $query) {
                    $query->whereNotIn('steps.short', ['general-data', 'heat-pump'])
                        ->whereNull('parent_id');
                })->with(['inputSource', 'step'])
                ->whereIn('input_source_id', $inputSources)
                ->forMe($user)
                ->get();

            $stepsToRecalculateChain = [];

            /** @var CompletedStep $completedStep */
            foreach ($completedSteps as $completedStep) {
                // user is interested, so recreate the advices for each step
                $stepsToRecalculateChain[] = new RecalculateStepForUser($user, $completedStep->inputSource, $completedStep->step);
            }

            if (!empty($stepsToRecalculateChain)) {
                ProcessRecalculate::withChain($stepsToRecalculateChain)->dispatch();
            }
        }
        $bar->finish();
    }
}
