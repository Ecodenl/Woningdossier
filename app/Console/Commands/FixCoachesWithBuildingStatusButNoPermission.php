<?php

namespace App\Console\Commands;

use App\Models\Building;
use App\Models\BuildingPermission;
use App\Models\User;
use App\Services\BuildingPermissionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixCoachesWithBuildingStatusButNoPermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:coaches-with-building-status-but-no-permission';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'If there are coaches that have a allowed building statuses, but no permission; this command will fix it';

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

        $coachesWithBuildingStatus = DB::select(
            DB::raw(
                "select `bcs2`.`coach_id`, `bcs2`.`building_id`, `bcs2`.`count_pending` as `count_pending`, `bcs3`.`count_removed` as `count_removed`
                        from (
                            SELECT coach_id, building_id, count(`status`) AS count_pending
                            FROM building_coach_statuses
                            WHERE `status` = 'added' and coach_id is not null
                            group by coach_id, building_id
                        ) AS bcs2 
                        left join (
                            SELECT building_id, coach_id, count(`status`) AS count_removed
                            FROM building_coach_statuses
                            WHERE `status` = 'removed' and coach_id is not null 
                            group by coach_id, building_id
                        ) AS bcs3 on `bcs2`.`building_id` = `bcs3`.`building_id` 
                        left join `buildings` on `bcs2`.`building_id` = `buildings`.`id` 
                        where (count_pending > count_removed) 
                        OR count_removed IS NULL
                        and `buildings`.`deleted_at` is null 
                        group by `building_id`, `coach_id`, `count_removed`, `count_pending`"
            )
        );

        $coachesWithStatusButNoPermission = [];
        foreach ($coachesWithBuildingStatus as $bcs) {
            $bp = BuildingPermission::where('building_id', $bcs->building_id)
                ->where('user_id', $bcs->coach_id)
                ->get();

            if ($bp->isEmpty()) {
                $coachesWithStatusButNoPermission[] = $bcs;
            }
        }

        $coachesWithNoAccess = [];
        foreach ($coachesWithStatusButNoPermission as $coachWithStatusButNoPermission) {

            $coachUser = User::forAllCooperations()->find($coachWithStatusButNoPermission->coach_id);
            $buildingFromResident = Building::find($coachWithStatusButNoPermission->building_id);

            // since it may have a deleted at..
            if ($buildingFromResident instanceof Building) {
                if ($coachUser->allow_access == false && $buildingFromResident->user->allow_access == true) {
                    $coachesWithNoAccess[] = ['user' => $coachUser, 'building' => $buildingFromResident];
                }
            }
        }

        foreach ($coachesWithNoAccess as $coachWithNoAccess) {
            $user = $coachWithNoAccess['user'];
            $building = $coachWithNoAccess['building'];

            $this->info("Giving user $user->id permission to building $building->id");
            BuildingPermissionService::givePermission($user, $building);
        }
    }
}
