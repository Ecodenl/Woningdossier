<?php

namespace App\Console\Commands\Upgrade\Merge;

use App\Models\Building;
use App\Models\Cooperation;
use App\Services\BuildingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        $mergeableCooperations = Cooperation::whereIn('slug', [
            'blauwvingerenergie',
            'cnme', // geen errors ?..
            'deltawind',
            'duec',
            'energiehuis',
            'leimuidenduurzaam',//  'geen errors'
            'lochemenergie', // geen errors
            'wijdemeren'
        ])->get();


        // so there are a couple custom queries we have to run in order to unduck the database
        // mostly consist of fixes for inconsistent user data that is not fixable by a query.
        Schema::disableForeignKeyConstraints();
        DB::table('users')
            ->where('account_id', 13511)
            ->update(['account_id' => 70024]);

        DB::table('users')
            ->where('account_id', 12808)
            ->update(['account_id' => 30001]);

        DB::table('users')
            ->where('account_id', 13676)
            ->update(['account_id' => 60006]);

        DB::table('accounts')->whereIn('id', [
            13511,
            12808,
            13676,
            9549 // this one will be copied from the sub_live environment.
        ])->delete();


        $building = Building::withTrashed()->find(4775);
        if ($building instanceof Building) {
            BuildingService::deleteBuilding($building);
        }

        Schema::enableForeignKeyConstraints();


        foreach ($mergeableCooperations as $mergeableCooperation) {
            $this->info("==={{$mergeableCooperation->slug}}===");
            // import the sub live environment.
            Artisan::call('db:wipe', ['--database' => 'sub_live']);
            $dump = storage_path("app/woonplan_{$mergeableCooperation->slug}.sql");
            if (!file_exists(storage_path("app/woonplan_{$mergeableCooperation->slug}.sql"))) {
                $this->error("No dump file found for {$mergeableCooperation->slug}");
                return 0;
            }

            $string = 'mysql -u %s -p%s %s < %s';
            $cmd = sprintf(
                $string,
                config('database.connections.sub_live.username'),
                config('database.connections.sub_live.password'),
                config('database.connections.sub_live.database'),
                $dump
            );
            exec($cmd);
            $this->info('Database dump imported');

            $commands = [
                DeleteSubLiveData::class,
                MergeUserAndBuildingTables::class,
                MergeAdjustedAutoIncrementTables::class,
            ];


            foreach ($commands as $command) {
                Artisan::call($command, ['cooperation' => $mergeableCooperation->slug]);
                $this->info("Completed {$command}");
            }
        }

        Schema::enableForeignKeyConstraints();
        $this->info("Merge is completed!");
    }
}
