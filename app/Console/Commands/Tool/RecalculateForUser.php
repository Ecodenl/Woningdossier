<?php

namespace App\Console\Commands\Tool;

use App\Helpers\Queue;
use App\Jobs\ProcessRecalculate;
use App\Jobs\RecalculateStepForUser;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Step;
use App\Models\User;
use App\Services\Models\NotificationService;
use Illuminate\Console\Command;
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
                                            {--cooperation= : Expects a cooperation ID, will be used to recalculate each user from the cooperation, can be combined with step short, input source and old advice params.}
                                            {--with-old-advices=true : If you want to keep the current categories, keep this set on true.}
                                            {--step-short=* : If you only want to recalculate specific steps, pass the shorts here.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command to calculate / recalculate advices for a user or cooperation';

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
        $userIds           = $this->option('user');
        $inputSourceShorts = $this->option('input-source');
        // default to resident.
        $inputSourcesToRecalculate = empty($inputSourceShorts) ? [InputSource::RESIDENT_SHORT] : $inputSourceShorts;
        $withOldAdvices            = $this->option('with-old-advices');
        $stepShorts                = $this->option('step-short');
        $cooperationId             = $this->option('cooperation');

        if (! is_null($cooperationId)) {
            $cooperation = Cooperation::find($cooperationId);
            if (! $cooperation instanceof Cooperation) {
                $this->error("No cooperation found for ID {$cooperationId}");
                return 0;
            } else {
                $this->info("Calculating each user for cooperation {$cooperation->name}...");
            }
            $users = User::forMyCooperation($cooperationId)->with('building')->get();
        } else {
            if (empty($userIds)) {
                $this->error("No user id's or cooperation id has been given..");
                return 0;
            }
            $users = User::findMany($userIds)->load('building');
        }

        if ($users->isEmpty()) {
            $this->error("No users found...");
            return 0;
        }

        $bar = $this->output->createProgressBar($users->count());

        $bar->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%%");
        $bar->setMessage('Queuing up the recalculate..');

        $inputSources = InputSource::whereIn('short', $inputSourcesToRecalculate)->get();

        $withOldAdvices = filter_var($withOldAdvices, FILTER_VALIDATE_BOOL);

        Log::debug("tool:recalculate");
        /** @var User $user */
        foreach ($users as $user) {
            $bar->advance(1);

            foreach ($inputSources as $inputSource) {
                $stepsToRecalculateChain = [];

                if (! empty($stepShorts)) {
                    $stepsToRecalculate = Step::recalculable()->whereIn('short', $stepShorts)->get();
                } else {
                    $stepsToRecalculate = Step::recalculable()->get();
                }

                foreach ($stepsToRecalculate as $stepToRecalculate) {
                    $stepsToRecalculateChain[] = (new RecalculateStepForUser($user, $inputSource, $stepToRecalculate,
                        $withOldAdvices))
                        ->onQueue(Queue::ASYNC);
                }

                Log::debug("Notification turned on for | b_id: {$user->building->id} | input_source_id: {$inputSource->id}");

                NotificationService::init()
                    ->forBuilding($user->building)
                    ->forInputSource($inputSource)
                    ->setType(RecalculateStepForUser::class)
                    ->setActive($stepsToRecalculate->count());

                Log::debug("Dispatching recalculate chain for | b_id: {$user->building->id} | input_source_id: {$inputSource->id}");

                ProcessRecalculate::withChain($stepsToRecalculateChain)
                    ->onQueue(Queue::ASYNC)
                    ->dispatch();
            }
        }
        $bar->finish();

        $this->output->newLine();
    }
}
