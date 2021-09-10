<?php

namespace App\Console\Commands\Upgrade;

use App\Models\CompletedStep;
use App\Models\InputSource;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateCompletedStepsForMasterInputSource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:update-completed-steps-for-master-input-source';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $master = InputSource::findByShort(InputSource::MASTER_SHORT);
        $completedSteps = DB::table('completed_steps')->where(
            'input_source_id',
            '!=',
            $master->id
        )->cursor();

        $bar = $this->output->createProgressBar($completedSteps->count());
        $bar->start();

        foreach ($completedSteps as $completedStep) {
            DB::table('completed_steps')->updateOrInsert([
                'input_source_id' => $master->id,
                'step_id'         => $completedStep->step_id,
                'building_id'     => $completedStep->building_id,
            ]);
            $bar->advance();
        }
        $bar->finish();
        $this->output->newLine();
    }
}
