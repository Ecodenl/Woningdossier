<?php

namespace App\Console\Commands;

use App\Models\BuildingCoachStatus;
use App\Models\BuildingPermission;
use App\Models\User;
use App\Models\Role;
use Illuminate\Console\Command;

class LegacyDeleteBuildingAccessOnCoachBuildings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'legacy:delete-building-access-on-coach-buildings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will delete coaches, that have access to a building from another coach.';

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
        $residentRole = Role::findByName('resident');

        $buildingIdsFromCoachesWithCoachesAttached = User::whereHas('roles', function ($q) use ($residentRole) {
            $q->where('role_id', $residentRole->id);
        })
            ->has('roles', '>', 1)
            ->whereHas('building.buildingCoachStatuses')
            ->with('building.buildingCoachStatuses')
            ->get()->pluck('building.id');

        BuildingCoachStatus::whereIn(
            'building_id',
            $buildingIdsFromCoachesWithCoachesAttached
        )->delete();

        $this->info('Deleted building coach statuses for the coach building itself.');

        // now get all the buildings from a coach, which gave building permission to someone.
        $buildingIdsFromCoachesWithBuildingPermissions = User::whereHas('roles', function ($q) use ($residentRole) {
            $q->where('role_id', $residentRole->id);
        })
            ->has('roles', '>', 1)
            ->whereHas('building.buildingPermissions')
            ->with('building.buildingPermissions')
            ->get()->pluck('building.id');

        BuildingPermission::whereIn('building_id', $buildingIdsFromCoachesWithBuildingPermissions)->delete();

        $this->info('Deleted building permissions for the coach building itself');
    }
}
