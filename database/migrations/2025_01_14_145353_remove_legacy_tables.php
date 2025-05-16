<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::whenTableHasColumn('building_features', 'building_heating_application_id', function (Blueprint $table) {
            $table->dropForeign(['building_heating_application_id']);
            $table->dropColumn('building_heating_application_id');
        });

        $tables = [
            'appliances',
            'appliance_building_services',
            'appliance_properties',
            'application_types',
            'attributes',
            'building_appliances',
            'building_current_heatings',
            'building_heating_applications',
            'central_heating_ages',
            'comfort_complaints',
            'cooperation_styles',
            'crawlspace_accesses',
            'experience_air_quality_in_homes',
            'features',
            'heat_sources',
            'interested_to_execute_measures',
            'interests',
            'motivations',
            'present_heat_pumps',
            'present_shower_wtws',
            'present_windows',
            'solar_water_heaters',
            'suffer_froms',
            'translations',
            'user_interests',
            'user_motivations',
            'ventilations',
        ];

        Schema::disableForeignKeyConstraints();

        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No. We're not going back to 2019.
    }
};
