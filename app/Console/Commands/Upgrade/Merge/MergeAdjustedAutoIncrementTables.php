<?php

namespace App\Console\Commands\Upgrade\Merge;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MergeAdjustedAutoIncrementTables extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:adjusted-auto-increment-tables {cooperation : The current cooperation database you want to merge eg; (deltawind into current)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge the tables that have incremented auto increments.';

    const TABLES = [
        'deltawind' => [

            'accounts' => 20000,
            'buildings' => 20000,
            'users' => 20000,
            'private_messages' => 70000,
            'questions' => 5000,
            'question_options' => 5000,
            'tool_question_answers' => 200000,
            'custom_measure_applications' => 5000,
            'questionnaires' => 200,
            'example_buildings' => 200,
            'media' => 200,
            'cooperation_measure_applications' => 600,
        ],

        'duec' => [

            'accounts' => 30000,
            'buildings' => 30000,
            'users' => 30000,
            'private_messages' => 100000,
            'questions' => 10000,
            'question_options' => 10000,
            'tool_question_answers' => 300000,
            'custom_measure_applications' => 10000,
            'questionnaires' => 400,
            'example_buildings' => 300,
            'media' => 300,
            'cooperation_measure_applications' => 700,
        ],
        'lochemenergie' => [

            'accounts' => 40000,
            'buildings' => 40000,
            'users' => 40000,
            'private_messages' => 130000,
            'questions' => 15000,
            'question_options' => 15000,
            'tool_question_answers' => 400000,
            'custom_measure_applications' => 15000,
            'questionnaires' => 600,
            'example_buildings' => 400,
            'media' => 400,
            'cooperation_measure_applications' => 800,
        ],

        'nhec' => [
            'accounts' => 50000,
            'buildings' => 50000,
            'users' => 50000,
            'private_messages' => 160000,
            'questions' => 20000,
            'question_options' => 20000,
            'tool_question_answers' => 500000,
            'custom_measure_applications' => 20000,
            'questionnaires' => 800,
            'example_buildings' => 500,
            'media' => 500,
            'cooperation_measure_applications' => 900,
        ],

        'energiehuis' => [

            'accounts' => 60000,
            'buildings' => 60000,
            'users' => 60000,
            'private_messages' => 190000,
            'questions' => 25000,
            'question_options' => 25000,
            'tool_question_answers' => 600000,
            'custom_measure_applications' => 25000,
            'questionnaires' => 1000,
            'example_buildings' => 600,
            'media' => 600,
            'cooperation_measure_applications' => 1000,
        ],

        'blauwvingerenergie' => [

            'accounts' => 70000,
            'buildings' => 70000,
            'users' => 70000,
            'private_messages' => 220000,
            'questions' => 30000,
            'question_options' => 30000,
            'tool_question_answers' => 700000,
            'custom_measure_applications' => 30000,
            'questionnaires' => 1200,
            'example_buildings' => 700,
            'media' => 700,
            'cooperation_measure_applications' => 1100,
        ],

        'leimuidenduurzaam' => [

            'accounts' => 80000,
            'buildings' => 80000,
            'users' => 80000,
            'private_messages' => 250000,
            'questions' => 35000,
            'question_options' => 35000,
            'tool_question_answers' => 800000,
            'custom_measure_applications' => 35000,
            'questionnaires' => 1400,
            'example_buildings' => 800,
            'media' => 800,
            'cooperation_measure_applications' => 1200,
        ],

        'wijdemeren' => [

            'accounts' => 90000,
            'buildings' => 90000,
            'users' => 90000,
            'private_messages' => 280000,
            'questions' => 40000,
            'question_options' => 40000,
            'tool_question_answers' => 900000,
            'custom_measure_applications' => 40000,
            'questionnaires' => 1600,
            'example_buildings' => 900,
            'media' => 900,
            'cooperation_measure_applications' => 1300,
        ],
        'cnme' => [

            'accounts' => 100000,
            'buildings' => 100000,
            'users' => 100000,
            'private_messages' => 310000,
            'questions' => 45000,
            'question_options' => 45000,
            'tool_question_answers' => 1000000,
            'custom_measure_applications' => 45000,
            'questionnaires' => 1800,
            'example_buildings' => 1000,
            'media' => 1000,
            'cooperation_measure_applications' => 1400,
        ],


    ];

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
        // the easy one, we can just pick the rows above the specified auto increment and insert them.
        $onlyAboveTables = [
            // old rows cant be updated or deleted
            'private_messages',
            // old rows can be updated, but since the old environment doesnt have this table there is no merge possible. aka only new cma are available
            'cooperation_measure_applications',
            'custom_measure_applications',
            'media',
            'tool_question_answers',
        ];

        // the deleted rows in these tables cant be recreated.
        // they may have been updated so we have to do that


        // hard case, these rows can have old rows and new ones. but we cant decide what is what.
        // this will be a slow one.
        $bogged = [
            'example_buildings',
            'questions',
            'question_options',
            'questionnaires',
        ];

        // the cooperation we are currently migrating
        $cooperationSlug = $this->argument('cooperation');
        $cooperation = DB::connection('sub_live')
            ->table('cooperations')
            ->where('slug', $cooperationSlug)
            ->first();

        // get the ids of the buildings / users from the sub live database
        // so we can delete the corresponding rows on the current migrated database
        $users = DB::connection('sub_live')
            ->table('users')
            ->where('cooperation_id', $cooperation->id)
            ->get();

        $userIds = $users->pluck('id')->toArray();
        $accountIds = $users->pluck('account_id')->toArray();

        $buildingIds = DB::connection('sub_live')
            ->table('buildings')
            ->whereIn('user_id', $userIds)
            ->pluck('id')->toArray();


        Schema::disableForeignKeyConstraints();


        // we made sure these rows were deleted before
        Log::debug("Starting migration for users table");
        $this->copyForTableInValues('users', 'id', $userIds);
        Log::debug("Starting migration for buildings table");
        $this->copyForTableInValues('buildings', 'id', $buildingIds);
        Log::debug("Starting migration for accounts table");
        $this->copyForTableInValues('accounts', 'id', $accountIds);
        // now we gotta do the accounts


        foreach (self::TABLES[$cooperationSlug] as $table => $autoIncremented) {

            // first set some defaults11
            $column = 'building_id';
            $ids = $buildingIds;

            // if the table has a user_id col, we will use the user_id as coll and userIds as values.
            // pretty obvious but ok.
            if (Schema::hasColumn($table, 'user_id')) {
                $column = 'user_id';
                $ids = $userIds;
            }

            $this->info("Starting migration for {$table}");
            if (in_array($table, $onlyAboveTables)) {
                $this->copyForTableAutoIncrement($table, 'id', $autoIncremented);
            }

//            if (in_array($table, $bogged)) {
//                $this->info("Table has a {$column} column");
//            }
        }
    }

    /**
     * Method to copy data from the sub_live connection to the db connection.
     * different from the other one as this copies the rows above or same as given auto increment.
     */
    private function copyForTableAutoIncrement(string $table, string $column, int $autoIncrement)
    {
        // gets all the column names, except the id coll.
        $columnNames = DB::table('information_schema.columns')
            ->selectRaw('column_name')
            ->where('table_schema', 'db')
            ->where('table_name', $table)
            ->pluck('column_name')
            ->map(fn($columnName) => "`$columnName`")
            ->implode(',');


        $sql = "insert into db.{$table} 
                        ({$columnNames})
                    select {$columnNames} 
                    from sub_live.{$table} 
                    where sub_live.{$table}.{$column} >= {$autoIncrement}";

        DB::getPdo()->prepare($sql)->execute();
    }

    /**
     * Method to copy data from the sub_live connection to the db connection.
     */
    private function copyForTableInValues(string $table, string $column, array $values)
    {
        // gets all the column names, except the id coll.
        $columnNames = DB::table('information_schema.columns')
            ->selectRaw('column_name')
            ->where('table_schema', 'db')
            ->where('table_name', $table)
            ->pluck('column_name')
            ->map(fn($columnName) => "`$columnName`")
            ->implode(',');


        $values = implode(',', $values);
        $sql = "insert into db.{$table} 
                        ({$columnNames})
                    select {$columnNames} 
                    from sub_live.{$table} 
                    where sub_live.{$table}.{$column} in ({$values})";

        DB::getPdo()->prepare($sql)->execute();
    }
}
