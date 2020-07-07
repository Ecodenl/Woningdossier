<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrateOldHybridFullHeatPumpServicesToNewServiceIdOnUserInterestsTable extends Migration
{
    use \App\Traits\DebugableMigrationTrait;
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->line(MigrateOldHybridFullHeatPumpServicesToNewServiceIdOnUserInterestsTable::class);

        $interestInIdsToMigrate = \DB::table('services')
            ->whereIn('short', ['hybrid-heat-pump', 'full-heat-pump'])
            ->select('id')
            ->get()->pluck('id')
            ->toArray();

        $users = \DB::table('users')->get();

        $heatPump = \DB::table('services')->where('short', 'heat-pump')->first();

        foreach ($users as $user) {
            $this->line('-------------------------------------------------');
            $this->line('migrating data for user_id: '.$user->id);
            $inputSources = \DB::table('input_sources')->get();
            foreach ($inputSources as $inputSource) {
                // get the lowest interest id, this is also the one with the lowest calculate value and therefore the most reliable one.
                $mostReliableInterestIdForHeatPumpService = \DB::table('user_interests')
                    ->where('user_id', $user->id)
                    ->where('input_source_id', $inputSource->id)
                    ->where('interested_in_type', 'service')
                    ->whereIn('interested_in_id', $interestInIdsToMigrate)
                    ->min('interest_id');

                if (!is_null($mostReliableInterestIdForHeatPumpService)) {
                    $this->line('setting interest_id: '.$mostReliableInterestIdForHeatPumpService.' for interested_in_id: '.$heatPump->id);
                    \DB::table('user_interests')
                        ->insert([
                            'user_id' => $user->id,
                            'input_source_id' => $inputSource->id,
                            'interested_in_type' => 'service',
                            'interested_in_id' => $heatPump->id,
                            'interest_id' => $mostReliableInterestIdForHeatPumpService
                        ]);
                }
            }
        }

        // all the hybrid / full heat pumps are now migrated to the new heat-pump service
        // so know we can safely delete the old interests
        \DB::table('user_interests')
            ->where('interested_in_type', 'service')
            ->whereIn('interested_in_id', $interestInIdsToMigrate)
            ->delete();

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
