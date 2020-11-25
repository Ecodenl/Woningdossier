<?php

namespace App\Console\Commands;

use App\Models\MeasureApplication;
use Illuminate\Console\Command;

class AddUserInterestsForCompletedSteps extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:interests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds default user interest level for steps that are completed, but have no interest yet';

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
        $completedStepsWithoutInterest = \DB::table('completed_steps as cs')
            ->select('cs.input_source_id', 'cs.step_id', 'cs.building_id', 'b.user_id', 'ui.interested_in_type', 'ui.interested_in_id', 'ui.interest_id')
            ->leftJoin('buildings as b', 'cs.building_id', '=', 'b.id')
            ->leftJoin('user_interests as ui', function ($join) {
                $join->on('ui.user_id', '=', 'b.user_id')
                    ->whereRaw('ui.input_source_id = cs.input_source_id')
                    ->whereRaw('ui.interested_in_id = cs.step_id')
                    ->whereRaw("ui.interested_in_type != 'App\\\Models\\\MeasureApplication'");
            })->whereNull('ui.interested_in_type')
            ->whereNotIn('step_id', [1, 12, 13, 14, 15])
            ->whereNull('b.deleted_at')
            ->get();

        $defaultInterest = \App\Models\Interest::where('calculate_value', 1)->first();
        if ($this->confirm("{$completedStepsWithoutInterest->count()} steps found without interest, proceed ?")) {

            $this->info('Adding user interests for steps...');
            $bar = $this->output->createProgressBar($completedStepsWithoutInterest->count());
            $bar->start();
            foreach ($completedStepsWithoutInterest as $completedStepWithoutInterest) {
                $bar->advance();
                \DB::table('user_interests')
                    ->insert([
                        'user_id' => $completedStepWithoutInterest->user_id,
                        'input_source_id' => $completedStepWithoutInterest->input_source_id,
                        'interested_in_id' => $completedStepWithoutInterest->step_id,
                        'interested_in_type' => \App\Models\Step::class,
                        'interest_id' => $defaultInterest->id,
                        'created_at' => \Carbon\Carbon::now(),
                        'updated_at' => \Carbon\Carbon::now()
                    ]);
            }
            $bar->finish();
        }
    }
}
