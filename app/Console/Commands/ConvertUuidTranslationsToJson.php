<?php

namespace App\Console\Commands;

use App\Models\Translation;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

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

        // Define enum for doctrine so we can do change calls on tables WITH an enum column
        DB::getDoctrineSchemaManager()
            ->getDatabasePlatform()
            ->registerDoctrineTypeMapping('enum', 'string');

        // We can't do anything if the translations table doesn't exist
        if (Schema::hasTable('translations')) {
            // Start a progress bar
            $this->info('Processing ' . count($tables) . ' tables...');
            $bar = $this->output->createProgressBar(count($tables));
            $bar->start();
            // Loop each table
            foreach ($tables as $table => $columns) {
                // We can't update a non-existing table
                if (Schema::hasTable($table)) {
                    $columns = is_array($columns) ? $columns : [$columns];

                    // Get all rows
                    $rows = DB::table($table)->get();

                    // Process each column
                    foreach ($columns as $column) {
                        // Can't do anything if the column doesn't exist
                        if (Schema::hasColumn($table, $column)) {
                            // We expect a CHAR with 36 maximum chars. This means we can't just set it to json
                            // We do 2 things: we alter the table to TEXT so we can fit the translation, and then
                            // set it to JSON
                            if ('json' !== Schema::getColumnType($table, $column)) {
                                Schema::table($table, function (Blueprint $table) use ($column) {
                                    $table->text($column)->change();
                                });
                            }

                            // Loop rows (which have the data from before the altering)
                            foreach ($rows as $row) {
                                // We can't update a non-existing property
                                if (property_exists($row, $column) && property_exists($row, 'id')) {
                                    $uuid = $row->{$column};
                                    // We need a valid uuid
                                    if (Str::isUuid($uuid)) {
                                        // Get translation(s)
                                        $translations = Translation::where('key', $uuid)
                                            ->pluck('translation', 'language')
                                            ->toArray();

                                        $data = [
                                            $column => json_encode($translations),
                                        ];

                                        // Set updated at if exists
                                        if (Schema::hasColumn($table, 'updated_at')) {
                                            $data['updated_at'] = Carbon::now();
                                        }

                                        // Update row with new json translations
                                        DB::table($table)->where('id', $row->id)
                                            ->update($data);
                                    }
                                }
                            }

                            // Convert column to json if not already json
                            // We have to cast to json AFTER converting the uuids, else we can't convert due to invalid
                            // json values
                            if ('json' !== Schema::getColumnType($table, $column)) {
                                Schema::table($table, function (Blueprint $table) use ($column) {
                                    $table->json($column)->change();
                                });
                            }
                        }
                    }
                }

                $bar->advance();
            }

            $bar->finish();
            $this->output->newLine();
            $this->info('All converted');
        } else {
            $this->error('Translations table not found');
        }
    }
}
