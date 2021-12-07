<?php

namespace App\Console\Commands\Upgrade\Merge;

use App\Console\Commands\Upgrade\Merge\MergeUserAndBuildingTables;
use App\Models\Cooperation;
use Illuminate\Console\Command;

class MergeDatabases extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:merge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merges the current sub live environments into the freshly migrated live database (deltawind.hoomdossier.nl env into hoomdossier.nl)';

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


//        foreach ($mergeableCooperations as $mergeableCooperation) {
            // import the sub live environment.
            $string = 'mysql -u %s -p%s %s < %s';
            $cmd = sprintf(
                $string,
                config('database.connections.sub_live.username'),
                config('database.connections.sub_live.password'),
                config('database.connections.sub_live.database'),
                storage_path("app/wijdemeren.sql")
            );
            exec($cmd);
            $this->info('Database dump imported');
//            $commands = [
//                MergeUserAndBuildingTables::class => ['--cooperation' => $mergeableCooperation->slug],
//            ];

//        }
    }
}
