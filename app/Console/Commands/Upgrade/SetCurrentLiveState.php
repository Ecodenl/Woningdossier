<?php

namespace App\Console\Commands\Upgrade;

use Illuminate\Console\Command;

class SetCurrentLiveState extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:set-current-live-state';

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
        $file = database_path('woonplanmigration/dump.sql');
        $database = config('database.connections.mysql.database');
        if ($database != 'woonplandossier'){
            $this->error("Not attempting to import on db other than woonplandossier");
            exit;
        }
        if (!file_exists($file)){
            $this->error("Current live state dump file not present at ". $file);
            exit;
        }
        $this->info("Importing database in copy from file '".$file."'..");
        $string = 'mysql -u %s -p%s %s < %s';
        $cmd = sprintf($string,
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            $database,
            $file);
        exec($cmd);
        $this->info('Database dump imported');
    }
}
