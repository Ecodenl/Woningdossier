<?php

namespace App\Console\Commands\Upgrade\Merge;

use App\Models\Cooperation;
use Illuminate\Console\Command;

class MergeNonRelationalTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'merge:non-relational-tables {cooperation : The current cooperation database you want to merge eg; (deltawind into current)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge the tables that have no building id or something';

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
        $tables = [
            'appliances',
            'appliance_building_services',
            'appliance_properties',
            'application_types',
            'assessment_types',
            'attributes',
            'building_categories',
            'building_current_heatings',
            'building_heatings',
            'building_heating_applications',
            'building_types',
            'building_type_categories',
            'building_type_element_max_savings',
            'central_heating_ages',
            'clients',
            'comfort_complaints',
            'comfort_level_tap_waters',
            'cooperations',
            'crawlspace_accesses',
            'device_options',
            'device_types',
            'elements',
            'element_values',
            'energy_labels',
            'experience_air_quality_in_homes',
            'facade_damaged_paintworks',
            'facade_plastered_surfaces',
            'facade_surfaces',
            'failed_jobs',
            'features',
            'file_types',
            'file_type_categories',
            'heater_component_costs',
            'heater_specifications',
            'heat_sources',
            'input_sources',
            'insulating_glazings',
            'interested_to_execute_measures',
            'interests',
            'jobs',
            'key_figure_boiler_efficiencies',
            'key_figure_consumption_tap_waters',
            'key_figure_temperatures',
            'language_lines',
            'measures',
            'measure_applications',
            'measure_categories',
            'measure_measure_category',
            'measure_properties',
            'measure_service_type',
            'mediables',
            'migrations',
            'model_has_permissions',
            'model_has_roles',
            'motivations',
            'notification_intervals',
            'notification_types',
            'paintwork_statuses',
            'password_resets',
            'permissions',
            'personal_access_tokens',
            'present_heat_pumps',
            'present_shower_wtws',
            'present_windows',
            'price_indexings',
            'pv_panel_location_factors',
            'pv_panel_orientations',
            'pv_panel_yields',
            'roles',
            'role_has_permissions',
            'roof_tile_statuses',
            'roof_types',
            'services',
            'service_types',
            'service_values',
            'solar_water_heaters',
            'space_categories',
            'statuses',
            'steps',
            'sub_steps',
            'sub_step_templates',
            'sub_step_tool_questions',
            'suffer_froms',
            'tool_questions',
            'tool_question_custom_values',
            'tool_question_types',
            'tool_question_valuables',
            'translations',
            'ventilations',
            'wood_rot_statuses',
        ];




//        foreach ($mergeableCooperations as $mergeableCooperation) {

//        }
    }
}
