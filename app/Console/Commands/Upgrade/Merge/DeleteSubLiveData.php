<?php

namespace App\Console\Commands\Upgrade\Merge;

use App\Models\Building;
use App\Models\Cooperation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DeleteSubLiveData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:delete-sub-live-data {cooperation : The cooperation that you want to cleanup on the db connection (delete data)}';

    /**
     * The console command description.
     *
     * @var string
     */

    protected $description = 'Deletes the data in the migrated database (on the db connection), that COULD be present in the sub live connection db. This way we dont get duplicate data.';

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
        $buildingIdTables = [
            'tool_question_answers',
            'completed_steps',
            'private_messages',
            'step_comments',
            'building_appliances',
            'building_elements',
            'building_services',
            'building_features',
            'building_heaters',
            'building_insulated_glazings',
            'building_pv_panels',
            'building_roof_types',
            'building_paintwork_statuses',

        ];
        $userIdTables = [
            'user_interests',
            'considerables',
            'user_energy_habits',
            'notification_settings',
            'user_motivations',
            'user_action_plan_advices',
        ];

        // first we will delete all the data of the cooperations on our migrated database.
        // After that we will merge the data from the corresponding  cooperation sub live database

        // make sure the foreign keys are enabled, we want to be noticed if something goes south in this command.
        Schema::enableForeignKeyConstraints();

        $cooperationSlug = $this->argument('cooperation');
        $cooperation = Cooperation::where('slug', $cooperationSlug)->first();

        $this->info("Deleting rows for cooperation {$cooperation->slug}");

        $userIds = $cooperation->users->pluck('id')->toArray();

        $buildingIds = Building::whereIn('user_id', $userIds)->pluck('id')->toArray();

        foreach ($buildingIdTables as $buildingTable) {
            $deleteCount = DB::table($buildingTable)->whereIn('building_id', $buildingIds)->delete();
            $this->info("Deleted {$deleteCount} {$buildingTable}");
        }

        foreach ($userIdTables as $userIdTable) {
            $deleteCount = DB::table($userIdTable)->whereIn('user_id', $userIds)->delete();
            $this->info("Deleted {$deleteCount} {$userIdTable}");
        }

        // and now we want to delete the actual buildings and user itself
        $deleteCount = DB::table('model_has_roles')->whereIn('model_id', $userIds)->delete();
        $this->info("Deleted {$deleteCount} model_has_roles");

        // and now we want to delete the actual buildings and user itself
        $deleteCount = DB::table('buildings')->whereIn('id', $buildingIds)->delete();
        $this->info("Deleted {$deleteCount} buildings");


        $db = config('database.connections.mysql.database');
        // we have 2 queries to delete the proper accounts

        // 1 for the existing account + users, aka the user and cooperation that already existed before the sub live environment
        // 1 for the existing account + NEW user.

        // this query retrieves the accounts from the db connection that match on
        // db.accounts.id = sub_live.accounts.id and db.accounts.email = sub_live.accounts.email
        // aka we know that this account is the same because of the email and the id are the same
        // that said, we can safely delete it from the db connection so we dont get duplicates.
        Schema::disableForeignKeyConstraints();
        $sql = "delete ".$db.".accounts 
                from sub_live.users
                join sub_live.accounts on sub_live.accounts.id = sub_live.users.account_id
                join ".$db.".accounts on db.accounts.id = sub_live.accounts.id and ".$db.".accounts.email = sub_live.accounts.email
                where sub_live.users.cooperation_id = {$cooperation->id}";

        DB::getPdo()->prepare($sql)->execute();

        // simple case, we can just get all the account ids through the cooperation.
        $accountIds = DB::table('users')->where('cooperation_id', $cooperation->id)->pluck('account_id')->toArray();

        $deleteCount = DB::table('users')->whereIn('id', $userIds)->delete();
        $this->info("Deleted {$deleteCount} users");



        $deleteCount = DB::table('accounts')
            ->whereIn('id', $accountIds)
            ->delete();

        $this->info("Deleted {$deleteCount} accounts");

        Schema::enableForeignKeyConstraints();

    }
}
