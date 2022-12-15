<?php

namespace App\Console\Commands;

use App\Console\Commands\Fixes\CorrectHasSolarPanelsToolQuestionAnswer;
use Illuminate\Console\Command;
use Illuminate\Database\Console\Seeds\SeedCommand;
use Illuminate\Support\Facades\DB;

class RunDeploy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deploy:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '
    This command contains code that should be ran during deployment.
    It also MUST contain atomic code to prevent problems in case this command isnt cleared for a new deployment.';

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

        // can be removed after one deploy cycle.
        if (!DB::table('measure_applications')
               ->where('short', '=', 'floor-insulation')
               ->where('costs', '=', 42)
               ->exists()) {
            // seeder has not been run yet.
            $this->call(SeedCommand::class,  ['--class' => 'MeasureApplicationsTableSeeder', '--force' => true]);
        }
        else {
            $this->info("You can remove the seed call for MeasureApplicationsTableSeeder from RunDeploy.php now.");
        }


//        $this->call(SeedCommand::class,  ['--class' => 'ToolQuestionTypesTableSeeder', '--force' => true]);
//
//        $commands = [
//            SeedCommand::class => ['--class' => 'ToolQuestionsTableSeeder', '--force' => true],
//        ];
//
//        foreach ($commands as $command => $arguments) {
//            $this->call($command, $arguments);
//        }

    }
}
