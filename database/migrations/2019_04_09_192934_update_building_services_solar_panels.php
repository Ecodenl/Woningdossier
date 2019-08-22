<?php

use Illuminate\Database\Migrations\Migration;

class UpdateBuildingServicesSolarPanels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $service = DB::table('services')->where('short', '=', 'total-sun-panels')->first();
        if ($service instanceof \stdClass) {
            $serviceId = $service->id;
            $solarPanelRows = DB::table('building_services')->where('service_id', '=', $serviceId)->get();
            if ($solarPanelRows instanceof \Illuminate\Support\Collection) {
                foreach ($solarPanelRows as $solarPanelRow) {
                    $extra = is_null($solarPanelRow->extra) ? '{}' : $solarPanelRow->extra;
                    $extra = json_decode($extra, true);

                    if (array_key_exists('value', $extra)) {
                        $extra['value'] = (int) $extra['value'];
                    }

                    if (! empty($extra)) {
                        DB::table('building_services')->where('id', '=', $solarPanelRow->id)->update(['extra' => json_encode($extra)]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Not needed
    }
}
