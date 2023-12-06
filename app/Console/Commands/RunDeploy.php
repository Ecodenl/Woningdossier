<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Console\Seeds\SeedCommand;

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
    It also MUST contain atomic code to prevent problems in case this command isn\'t cleared for a new deployment.';

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
        \DB::table('language_lines')
            ->where('group', 'pdf/user-report')
            ->where('key', 'pages.front-page.title')
            ->update([
                'text' => json_encode(['nl' =>  __('pdf/user-report.pages.front-page.title')]),
            ]);

        $commands = [
            SeedCommand::class => [
                ['--class' => 'ToolLabelsTableSeeder', '--force' => true],
                ['--class' => 'ToolQuestionsTableSeeder', '--force' => true],
                ['--class' => 'SubSteppablesTableSeeder', '--force' => true],
            ],
        ];

        foreach ($commands as $command => $variants) {
            if (empty($variants)) {
                $this->call($command);
            }

            foreach ($variants as $arguments) {
                $this->call($command, $arguments);
            }
        }
    }
}
