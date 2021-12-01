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

        // first we will delete all the data of the cooperations on our migrated database.
        // After that we will merge the data from the corresponding  cooperation sub live database
        /** @var Cooperation $mergeableCooperation */
        foreach ($mergeableCooperations as $mergeableCooperation) {
            $mergeableCooperation->users()->delete();
            $userIds = [];
            $buildingIds = [];

            DB::table('completed_steps')->whereIn('building_id', $buildingIds)->delete();
            // delete the private messages from the cooperation
            DB::table('private_messages')->whereIn('building_id', $buildingIds)->delete();

            DB::table('step_comments')->whereIn('building_id', $buildingIds)->delete();

            // table will be removed anyways.
            DB::table('building_appliances')->whereIn('building_id', $buildingIds)->delete();

            DB::table('user_action_plan_advices')->whereIn('user_id', $userIds)->delete();

            // remove the user interests
            // we keep the user interests table until we are 100% sure it can be removed
            // but because of gdpr we have to keep this until the table is removed
            DB::table('user_interests')->whereIn('user_id', $userIds)->delete();
            // we cant use the relationship because we just want to delete everything
            DB::table('considerables')->whereIn('user_id', $userIds)->delete();
            // remove the energy habits from a user
            DB::table('user_energy_habits')->whereIn('user_id', $userIds)->delete();
            // remove the notification settings
            DB::table('notification_settings')->whereIn('user_id', $userIds)->delete();
            // first detach the roles from the user
            DB::table('model_has_roles')->whereIn('model_id', $userIds)->delete();
            DB::table('user_motivations')->whereIn('model_id', $userIds)->delete();


            DB::table('users')->whereIn('user_id', $userIds)->delete();
            DB::table('tool_question_answers')->whereIn('user_id', $userIds)->delete();

        }

        // this deletes all the accounts related to the sub live environments
        $sql = `delete accounts from cooperations
                left join users on users.cooperation_id = cooperations.id
                left join accounts on accounts.id = users.account_id
                where cooperations.slug in ('blauwvingerenergie',
                        'cnme',
                        'deltawind',
                        'duec',
                        'energiehuis',
                        'leimuidenduurzaam',
                        'lochemenergie',
                        'nhec',
                        'wijdemeren')

            `;

        DB::raw($sql);

    }
}
