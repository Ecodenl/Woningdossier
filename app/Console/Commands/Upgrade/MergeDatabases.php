<?php

namespace App\Console\Commands\Upgrade;

use App\Models\Account;
use App\Models\Building;
use App\Models\Considerable;
use App\Models\Cooperation;
use App\Models\User;
use App\Services\BuildingService;
use Illuminate\Console\Command;
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
    protected $description = 'Merges the current live environments into the freshly migrated live database (deltawind.hoomdossier.nl env into hoomdossier.nl)';

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

        // the cooperations which have sub live environments
        $mergeableCooperations = Cooperation::whereIn('slug', [
            'blauwvingerenergie',
            'cnme',
            'deltawind',
            'duec',
            'energiehuis',
            'leimuidenduurzaam',
            'lochemenergie',
            'nhec',
            'wijdemeren'
        ])->get();


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
        /** @var Cooperation $mergeableCooperation */
        Schema::enableForeignKeyConstraints();
        foreach ($mergeableCooperations as $mergeableCooperation) {
            $this->info("Deleting rows for cooperation {$mergeableCooperation->slug}");

            $userIds = $mergeableCooperation->users->pluck('id')->toArray();

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

            $deleteCount = DB::table('users')->whereIn('id', $userIds)->delete();
            $this->info("Deleted {$deleteCount} users");

        }

        Schema::disableForeignKeyConstraints();

        // this deletes all the accounts related to the sub live environments
        // these will all be reimported anyway so it doesnt matter anyway.
        $deleteCount = DB::table('accounts')
            ->leftJoin('users', 'users.account_id', '=', 'accounts.id')
            ->leftJoin('cooperations', 'cooperations.id', '=', 'users.cooperation_id')
            ->whereIn('cooperations.slug', $mergeableCooperations->pluck('slug')->toArray())
            ->delete();

        $this->info("Deleted {$deleteCount} accounts");

    }
}
