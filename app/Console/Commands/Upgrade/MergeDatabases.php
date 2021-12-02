<?php

namespace App\Console\Commands\Upgrade;

use Illuminate\Console\Command;

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
    protected $description = 'Merges the current sub live environments into the freshly migrated live database (deltawind.hoomdossier.nl env into hoomdossier.nl)';

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
        // import the sub live environment.
        $string = 'mysql -u %s -p%s %s < %s';
        $cmd = sprintf(
            $string,
            config('database.connections.migration.username'),
            config('database.connections.migration.password'),
            config('database.connections.migration.database'),
            storage_path('app/wijdemeren.sql')
        );
        exec($cmd);
        $this->info('Database dump imported');

        $withAutoIncrements = [

            'building_heating_applications',
            'building_heatings',
            'building_type_categories',

            'central_heating_ages',
            'clients',
            'comfort_complaints',
            'comfort_level_tap_waters',
            'cooperations',
            'crawlspace_accesses',

            'element_values',
            'elements',
            'energy_labels',
            'example_buildings',
            'experience_air_quality_in_homes',
            'facade_damaged_paintworks',
            'facade_plastered_surfaces',
            'facade_surfaces',
            'file_type_categories',
            'file_types',
            'heater_specifications',
            'input_sources',
            'insulating_glazings',
            'interests',


            'key_figure_boiler_efficiencies',
            'key_figure_consumption_tap_waters',
            'key_figure_temperatures',
            'language_lines',
            'logs',
            'measure_applications',
            'motivations',
            'notification_intervals',
            'notification_types',

            'paintwork_statuses',
            'permissions',
            'present_heat_pumps',

            'price_indexings',
            'pv_panel_location_factors',
            'pv_panel_orientations',
            'pv_panel_yields',


            'roles',
            'roof_tile_statuses',
            'roof_types',
            'service_values',
            'services',
            'statuses',
            'steps',
            'ventilations',
            'wood_rot_statuses',

        ];
        $withoutAutoIncrements = [
            'building_appliances',
            'building_categories',
            'building_coach_statuses',
            'building_current_heatings',
            'building_elements',
            'building_features',
            'building_heaters',

            'building_insulated_glazings',
            'building_notes',
            'building_paintwork_statuses',
            'building_permissions',
            'building_pv_panels',
            'building_roof_types',
            'building_services',
            'building_statuses',
            'building_type_element_max_savings',
            'building_types',
            'building_ventilations',

            'completed_questionnaires',
            'completed_steps',
            'completed_sub_steps',
            'considerables',

            'cooperation_steps',
            'cooperation_styles',
            'example_building_contents',
            'mediables',
            'model_has_permissions',
            'model_has_roles',

            'notification_settings',
            'notifications',
            'personal_access_tokens',
            'private_message_views',

            'questions_answers',

            'role_has_permissions',
            'step_comments',
            'sub_step_templates',

            'tool_question_answers',

            'user_action_plan_advice_comments',
            'user_action_plan_advices',
            'user_energy_habits',
        ];

        $truncateTables = [
            'failed_jobs',
            'file_storages',
            'jobs',
        ];

        $adjustedAutoIncrementedTables = [
            'media',
            'private_messages',
            'question_options',
            'questionnaires',
            'questions',
            'custom_measure_applications',
            'cooperation_measure_applications',
            'accounts',
            'buildings',
            'users',
        ];

        $shouldDeleteTables = [
            'appliance_building_services',
            'appliance_properties',
            'appliances',
            'application_types',
            'assessment_types',
            'attributes',
            'device_options',
            'device_types',
            'devices',
            'features',
            'heat_sources',
            'heater_component_costs',
            'interested_to_execute_measures',
            'measure_categories',
            'measure_measure_category',
            'measure_properties',
            'measure_service_type',
            'measures',
            'present_shower_wtws',
            'service_types',
            'solar_water_heaters', // @patrick double check
            'space_categories',
            'suffer_froms',
            // deleting this table comes with alot of refactor work (probably) as this was something important in the "old" tool
            'tool_settings',
            'user_interests',
            'user_motivations',
            'present_windows',
        ];

        $tablesToDoNothingWith = [
            'sub_step_tool_questions',
            'sub_steps',

            'tool_question_custom_values',
            'tool_question_types',
            'tool_question_valuables',
            'tool_questions',
            'tool_settings',
            'translations',
            // figure out how we gonna do this
            'password_resets',
            'migrations',
        ];

    }
}
