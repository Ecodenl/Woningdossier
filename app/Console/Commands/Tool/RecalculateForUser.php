<?php

namespace App\Console\Commands\Tool;

use App\Helpers\StepHelper;
use App\Models\CompletedStep;
use App\Models\Cooperation;
use App\Models\Step;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class RecalculateForUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tool:recalculate 
                                            {--user=* : The ID\'s of the users } 
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

        $this->output->progressStart($users->count());

        /** @var User $user */
        foreach ($users as $user) {
            if (!in_array($user->id, [504, 854, 1036])) {
                // get the completed steps for a user.
                $completedSteps = $user->building
                    ->completedSteps()
                    ->whereHas('step', function (Builder $query) {
                        $query->whereNotIn('steps.short', ['general-data', 'heat-pump'])
                            ->whereNull('parent_id');
                    })->with(['inputSource', 'step'])
                    ->forMe($user)
                    ->get();

                /** @var CompletedStep $completedStep */
                foreach ($completedSteps as $completedStep) {
                    // check if the user is interested in the step
                    if (StepHelper::hasInterestInStep($user, Step::class, $completedStep->step->id, $completedStep->inputSource)) {
                        // user is interested, so recreate the advices for each step
                        $stepClass = 'App\\Helpers\\Cooperation\Tool\\' . Str::singular(Str::studly($completedStep->step->short)) . 'Helper';
                        $stepHelperClass = new $stepClass($user, $completedStep->inputSource);
                        $stepHelperClass->createValues()->createAdvices();
                    }
                }
            }
            $this->output->progressAdvance(1);
        }
        $this->output->progressFinish();
    }
}
