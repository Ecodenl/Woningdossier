<?php

namespace App\Console\Commands\Upgrade\Merge;

use App\Models\Cooperation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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
        $tablesThatNeedUpdate = [
            'accounts',
            'buildings',
            'users',
        ];

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
//        foreach (self::TABLES[$cooperationSlug] as $table => $autoIncremented) {
//
//            // first set some defaults
//            $column = 'building_id';
//            $ids = $buildingIds;
//
//            // if the table has a user_id col, we will use the user_id as coll and userIds as values.
//            // pretty obvious but ok.
//            if (Schema::hasColumn($table, 'user_id')) {
//                $column = 'user_id';
//                $ids = $userIds;
//            }
//
//            $this->info("Starting migration for {$table}");
//            if (in_array($table, $onlyAboveTables)) {
////                $this->copyForTable($table, 'id', $autoIncremented);
//            }
//
//            if (in_array($table, $bogged)) {
//                $this->info("Table has a {$column} column");
//            }
//        }

        // unfortunately we need custom queries to update the buildings, accounts and users table.
        $updateStatementForUsersTable = "UPDATE db.users as t1, sub_live.users as t2
                SET t1.`account_id` = t2.`account_id`, t1.`cooperation_id` = t2.`cooperation_id`, t1.`first_name` = t2.`first_name`, t1.`last_name` = t2.`last_name`, t1.`phone_number` = t2.`phone_number`, t1.`extra` = t2.`extra`, t1.`allow_access` = t2.`allow_access`, t1.`created_at` = t2.`created_at`, t1.`updated_at` = t2.`updated_at`
                WHERE t1.id = t2.id and t1.cooperation_id = :cooperation_id";

        $implodedUserIds = implode(',', $userIds);
        $updateStatementForBuildingsTable = "UPDATE db.buildings as t1, sub_live.buildings as t2
                SET t1.`user_id` = t2.`user_id`, t1.`street` = t2.`street`, t1.`number` = t2.`number`, t1.`extension` = t2.`extension`, t1.`city` = t2.`city`, t1.`postal_code` = t2.`postal_code`, t1.`country_code` = t2.`country_code`, t1.`owner` = t2.`owner`, t1.`primary` = t2.`primary`, t1.`bag_addressid` = t2.`bag_addressid`, t1.`created_at` = t2.`created_at`, t1.`updated_at` = t2.`updated_at`, t1.`deleted_at` = t2.`deleted_at`
                WHERE t1.id = t2.id and t1.user_id in ({$implodedUserIds})";


        $implodedAccountIds = implode(',', $accountIds);
        $updateStatementForAccountsTable =
            "UPDATE db.accounts as t1, sub_live.accounts as t2
                SET t1.`email` = t2.`email`, t1.`password` = t2.`password`, t1.`remember_token` = t2.`remember_token`, t1.`email_verified_at` = t2.`email_verified_at`, t1.`old_email` = t2.`old_email`, t1.`old_email_token` = t2.`old_email_token`, t1.`active` = t2.`active`, t1.`is_admin` = t2.`is_admin`, t1.`created_at` = t2.`created_at`, t1.`updated_at` = t2.`updated_at`
                WHERE t1.id = t2.id and t1.id in ({$implodedAccountIds})";

//        dd($userIds, $accountIds, $updat);
        DB::getPdo()
            ->prepare($updateStatementForUsersTable)
            ->execute(['cooperation_id' => $cooperation->id]);


    }

    /**
     * Method to copy data from the sub_live connection to the db connection.
     * different from the other one as this copies the rows above or same as given auto increment.
     */
    private function copyForTable(string $table, string $column, int $autoIncrement)
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
}
