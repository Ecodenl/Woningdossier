<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class ConvertUuidTranslationsToJson extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:convert-uuid-to-json';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converts all uuid translations to json columns';

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
            'assessment_types' => 'name',
            'building_categories' => 'name',
            'building_heatings' => 'name',
            'building_heating_applications' => 'name',
            'building_types' => 'name',
            'comfort_level_tap_water' => 'name',
            'crawlspace_accesses' => 'name',
            'elements' => 'name',
            'element_values' => 'value',
            'example_buildings' => 'name',
            'facade_damaged_paintworks' => 'name',
            'facade_plastered_surfaces' => 'name',
            'facade_surfaces' => [
                'name',
                'execution_term_name',
            ],
            'file_types' => 'name',
            'file_type_categories' => 'name',
            'insulating_glazings' => 'name',
            'interests' => 'name',
            'measures' => 'name',
            'measure_applications' => [
                'measure_name',
                'cost_unit',
                'maintenance_unit',
            ],
            'measure_categories' => 'name',
            'motivations' => 'name',
            'notification_intervals' => 'name',
            'notification_types' => 'name',
            'paintwork_statuses' => 'name',
            'price_indexings' => 'name',
            'pv_panel_orientations' => 'name',
            'questions' => 'name',
            'question_options' => 'name',
            'questionnaires' => 'name',
            'roof_tile_statuses' => 'name',
            'roof_types' => 'name',
            'services' => [
                'name',
                'info',
            ],
            'service_types' => 'name',
            'service_values' => 'value',
            'space_categories' => 'name',
            'statuses' => 'name',
            'wood_rot_statuses' => 'name',
        ];

        // We can't do anything if the translations table doesn't exist
        if (Schema::hasTable('translations')) {
            $this->info('Processing');
            $bar = $this->output->createProgressBar(count($tables));

            foreach($tables as $table => $columns) {
                // We can't update a non-existing table
                if (Schema::hasTable($table)) {
                    $columns = is_array($columns) ? $columns : [$columns];

                }

                $bar->advance();
            }

            $bar->finish();
            $this->output->newLine();

        } else {
            $this->error('Translations table not found');
        }
    }
}
