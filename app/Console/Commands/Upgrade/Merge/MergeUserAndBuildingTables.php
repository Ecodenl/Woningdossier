<?php

namespace App\Console\Commands\Upgrade\Merge;

use App\Models\Cooperation;
use Illuminate\Console\Command;
use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MergeUserAndBuildingTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:user-building-tables {cooperation : The current cooperation database you want to merge eg; (deltawind into current)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge the tables that have a building or user id.';

    const TABLES = [
//        'building_appliances',
//        'building_coach_statuses',
//        'building_elements',
//        'building_features',
//        'building_heaters',
//        'building_insulated_glazings',
//        'building_notes',
//        'building_paintwork_statuses',
//        'building_permissions',
//        'building_pv_panels',
//        'building_roof_types',
//        'building_services',
//        'building_statuses',
//        'building_ventilations',
//        'completed_questionnaires',
//        'completed_steps',
//        'completed_sub_steps',
//        'considerables',
//        'devices',
//        'example_building_contents',
//        'file_storages',
//        'logs',
//        'notifications',
//        'notification_settings',
//        'private_message_views',
//        'questions_answers',
//        'step_comments',
//        'tool_question_answers',
//        'tool_settings',
//        'user_action_plan_advices',
//        'user_action_plan_advice_comments',
        'user_energy_habits',
//        'user_interests',
//        'user_motivations',
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
        // the cooperation we are currently migrating
        $cooperationSlug = $this->argument('cooperation');
        $cooperation = DB::connection('sub_live')
            ->table('cooperations')
            ->where('slug', $cooperationSlug)
            ->first();

        // get the ids of the buildings / users from the sub live database
        // so we can delete the corresponding rows on the current migrated database
        $userIds = DB::connection('sub_live')
            ->table('users')
            ->where('cooperation_id', $cooperation->id)
            ->pluck('id')->toArray();

        $buildingIds = DB::connection('sub_live')
            ->table('buildings')
            ->whereIn('user_id', $userIds)
            ->pluck('id')->toArray();

        foreach (self::TABLES as $table) {

            $columnNames = DB::table('information_schema.columns')
                ->selectRaw('group_concat(COLUMN_NAME) as concat_string')
                ->where('table_schema', 'db')
                ->where('table_name', $table)
                ->where('column_name', '!=', 'id')
                ->groupBy('table_name')
                ->pluck('concat_string')
                ->first();

            // first delete the rows
            // and insert the data afterwards
            if (Schema::hasColumn($table, 'user_id')) {
                DB::table($table)->whereIn('user_id', $userIds)->delete();

                Schema::disableForeignKeyConstraints();
                $db = DB::getPdo();

                $userIds = implode(',', $userIds);
                $sql = "insert into db.{$table} 
                        ({$columnNames})
                    select {$columnNames} 
                    from sub_live.{$table} 
                    where sub_live.{$table}.user_id in ({$userIds})";
                $db->prepare($sql)->execute();

            } else {
                DB::table($table)->whereIn('building_id', $buildingIds)->delete();
            }
        }
    }
}
