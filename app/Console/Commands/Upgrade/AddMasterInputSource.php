<?php

namespace App\Console\Commands\Upgrade;

use App\Helpers\ObjectHelper;
use App\Models\Building;
use App\Models\InputSource;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class AddMasterInputSource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:add-master-input-source {id?*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensures each answer has a master input source';

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
        $ids = $this->argument('id');
        $buildings = empty($ids) ? Building::all() : Building::whereIn('id', $ids)->get();

        $this->info('Starting process to add/update master input source on all/given buildings...');
        $bar = $this->output->createProgressBar($buildings->count());
        $bar->start();

        foreach ($buildings as $building) {
            Log::debug("Processing master input source for building {$building->id}.");
            $this->setMasterInputSources($building);
            $bar->advance();
        }

        $bar->finish();
        $this->output->newLine();
        $this->info('All done.');
    }

    /**
     * Set the master input sources for a building in all relevant tables
     *
     * @param  \App\Models\Building  $building
     */
    public function setMasterInputSources(Building $building)
    {
        $tables = [
            'building_elements' => [
                'where_column' => 'element_id',
                'answer_columns' => [
                    'element_value_id',
                    'extra',
                ],
            ],
            'building_services' => [
                'where_column' => 'service_id',
                'answer_columns' => [
                    'service_value_id',
                    'extra',
                ],
            ],
            'user_interests' => [
                'where_column' => 'interested_in_type',
                'additional_where_column' => 'interested_in_id',
                'answer_columns' => [
                    'interest_id',
                ],
            ],
            'user_action_plan_advices' => [
                'where_column' => 'step_id',
                'additional_where_column' => 'measure_application_id',
                'answer_columns' => [
                    'costs',
                    'savings_gas',
                    'savings_electricity',
                    'savings_money',
                    'year',
                    'planned',
                    'planned_year',
                ],
            ],
            'building_features' => [
                'answer_columns' => [
                    'building_heating_application_id',
                    'building_category_id',
                    'building_type_id',
                    'roof_type_id',
                    'energy_label_id',
                    'cavity_wall',
                    'wall_surface',
                    'insulation_wall_surface',
                    'facade_plastered_painted',
                    'wall_joints',
                    'contaminated_wall_joints',
                    'element_values',
                    'facade_plastered_surface_id',
                    'facade_damaged_paintwork_id',
                    'surface',
                    'floor_surface',
                    'insulation_surface',
                    'window_surface',
                    'volume',
                    'build_year',
                    'building_layers',
                    'monument',
                ],
            ],
            'building_paintwork_statuses' => [
                'answer_columns' => [
                    'last_painted_year',
                    'paintwork_status_id',
                    'wood_rot_status_id',
                ],
            ],
            'building_ventilations' => [
                'answer_columns' => [
                    'how',
                    'living_situation',
                    'usage',
                ],
            ],
            'building_pv_panels' => [
                'answer_columns' => [
                    'total_installed_power',
                    'peak_power',
                    'number',
                    'pv_panel_orientation_id',
                    'angle',
                ],
            ],
            'building_heaters' => [
                'answer_columns' => [
                    'pv_panel_orientation_id',
                    'angle',
                ],
            ],
            'building_appliances' => [
                'answer_columns' => [
                    'appliance_id',
                ],
            ],
            'user_energy_habits' => [
                'answer_columns' => [
                    'resident_count',
                    'thermostat_high',
                    'thermostat_low',
                    'hours_high',
                    'heating_first_floor',
                    'heating_second_floor',
                    'heated_space_outside',
                    'cook_gas',
                    'water_comfort_id',
                    'amount_electricity',
                    'amount_gas',
                    'amount_water',
                    'renovation_plans',
                    'building_complaints',
                    'start_date',
                    'end_date',
                ],
            ],
            'building_roof_types' => [
                'where_column' => 'roof_type_id',
                'answer_columns' => [
                    'element_value_id',
                    'roof_surface',
                    'insulation_roof_surface',
                    'zinc_surface',
                    'building_heating_id',
                ],
            ],
            'building_insulated_glazings' => [
                'where_column' => 'measure_application_id',
                'answer_columns' => [
                    'insulating_glazing_id',
                    'building_heating_id',
                    'm2',
                    'windows',
                    'extra',
                ],
            ],
            'questions_answers' => [
                'where_column' => 'question_id',
                'answer_columns' => [
                    'answer',
                ],
            ],
        ];

        // Get the input sources
        $masterInputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
        $coachInputSource = InputSource::findByShort(InputSource::COACH_SHORT);
        $residentInputSource = InputSource::findByShort(InputSource::RESIDENT_SHORT);

        foreach ($tables as $table => $tableData) {
            if (Schema::hasColumn($table, 'user_id')) {
                $buildingOrUserId = $building->user->id;
                $buildingOrUserColumn = 'user_id';
            } else {
                $buildingOrUserId = $building->id;
                $buildingOrUserColumn = 'building_id';
            }

            // Get all values for the current building for resident and coach input source
            $values = DB::table($table)
                ->where($buildingOrUserColumn, $buildingOrUserId)
                ->whereIn('input_source_id', [$coachInputSource->id, $residentInputSource->id])
                ->get();

            if ($values->isNotEmpty()) {
                $answerColumns = $tableData['answer_columns'];
                $whereColumn = $tableData['where_column'] ?? null;
                $additionalWhereColumn = $tableData['additional_where_column'] ?? null;

                $differentiatingValues = [];
                $differentiatingSubValues = [];

                // Set conditional values
                if (! is_null($whereColumn)) {
                    // There is a where column. This means multiple values per input source. Let's grab them
                    $differentiatingValues = $values->pluck($whereColumn)->unique()->toArray();

                    if (! is_null($additionalWhereColumn)) {
                        // There is an additional column... Let's grab these too
                        foreach ($differentiatingValues as $differentiatingValue) {
                            $differentiatingSubValues[$differentiatingValue] = $values
                                ->where($whereColumn, $differentiatingValue)
                                ->pluck($additionalWhereColumn)->unique()->toArray();
                        }
                    }
                }

                $masterInputSourceAnswers = [];

                // We will loop all the answer columns, as we must check them individually
                foreach ($answerColumns as $answerColumn) {
                    if (! is_null($whereColumn)) {
                        foreach ($differentiatingValues as $differentiatingValue) {
                            if (! is_null($additionalWhereColumn)) {
                                foreach ($differentiatingSubValues[$differentiatingValue] as $differentiatingSubValue) {
                                    // Grab the answer of the coach
                                    $coachAnswer = $this->searchCollectionForValue($values, $coachInputSource,
                                        [
                                            $whereColumn => $differentiatingValue,
                                            $additionalWhereColumn => $differentiatingSubValue,
                                        ]);

                                    $answer = ObjectHelper::getObjectProperty($coachAnswer, $answerColumn);

                                    if (empty($answer) && ! is_numeric($answer)) {
                                        // Grab the answer of the resident if answer is TRULY empty
                                        $residentAnswer = $this->searchCollectionForValue($values, $residentInputSource,
                                            [
                                                $whereColumn => $differentiatingValue,
                                                $additionalWhereColumn => $differentiatingSubValue,
                                            ]);

                                        $answer = ObjectHelper::getObjectProperty($residentAnswer, $answerColumn);
                                    }

                                    // Build answer structure with where and additional where
                                    $masterInputSourceAnswers[$answerColumn][$whereColumn][$differentiatingValue][$additionalWhereColumn][$differentiatingSubValue] = $answer;
                                }
                            } else {
                                // Grab the answer of the coach
                                $coachAnswer = $this->searchCollectionForValue($values, $coachInputSource,
                                    [$whereColumn => $differentiatingValue]);

                                $answer = ObjectHelper::getObjectProperty($coachAnswer, $answerColumn);

                                if (empty($answer) && ! is_numeric($answer)) {
                                    // Grab the answer of the resident if answer is TRULY empty
                                    $residentAnswer = $this->searchCollectionForValue($values, $residentInputSource,
                                        [$whereColumn => $differentiatingValue]);

                                    $answer = ObjectHelper::getObjectProperty($residentAnswer, $answerColumn);
                                }

                                // Build answer structure with where
                                $masterInputSourceAnswers[$answerColumn][$whereColumn][$differentiatingValue] = $answer;
                            }
                        }
                    } else {
                        // Grab the answer of the coach
                        $coachAnswer = $this->searchCollectionForValue($values, $coachInputSource);

                        $answer = ObjectHelper::getObjectProperty($coachAnswer, $answerColumn);

                        if (empty($answer) && ! is_numeric($answer)) {
                            // Grab the answer of the resident if answer is TRULY empty
                            $residentAnswer = $this->searchCollectionForValue($values, $residentInputSource);

                            $answer = ObjectHelper::getObjectProperty($residentAnswer, $answerColumn);
                        }

                        // Build default answer structure
                        $masterInputSourceAnswers[$answerColumn] = $answer;
                    }
                }

                // We now have the structure for the row(s) of answers we need to put under the master input source
                // Structure is default column => answer
                // With one where: column => [whereColumn => [whereColumnValue => answer]]
                // With additional where: column => [whereColumn => [whereColumnValue => additionalWhereColumn => [additionalWhereColumnValue => answer]]]

                // Default logic
                $baseUpdateOrInsertLogic = [
                    $buildingOrUserColumn => $buildingOrUserId,
                    'input_source_id' => $masterInputSource->id
                ];

                if (is_null($whereColumn)) {
                    // Default structure, easy pickins!
                    $answersToInsert = $masterInputSourceAnswers;
                    DB::table($table)
                        ->updateOrInsert($baseUpdateOrInsertLogic, $answersToInsert);
                } else {
                    if (is_null($additionalWhereColumn)) {
                        // Only where column
                        foreach ($differentiatingValues as $differentiatingValue) {
                            $answersToInsert = [];

                            // Set answers
                            foreach ($masterInputSourceAnswers as $answerColumn => $answers) {
                                $answersToInsert[$answerColumn] = $answers[$whereColumn][$differentiatingValue];
                            }

                            // Set custom logic for insert
                            $customLogic = $baseUpdateOrInsertLogic;
                            $customLogic[$whereColumn] = $differentiatingValue;

                            // Insert for each where
                            DB::table($table)
                                ->updateOrInsert($customLogic, $answersToInsert);
                        }
                    } else {
                        // With additional where column
                        foreach ($differentiatingValues as $differentiatingValue) {
                            foreach ($differentiatingSubValues[$differentiatingValue] as $differentiatingSubValue) {
                                $answersToInsert = [];

                                // Set answers
                                foreach ($masterInputSourceAnswers as $answerColumn => $answers) {
                                    $answersToInsert[$answerColumn] = $answers[$whereColumn][$differentiatingValue][$additionalWhereColumn][$differentiatingSubValue];
                                }

                                // Set custom logic for insert
                                $customLogic = $baseUpdateOrInsertLogic;
                                $customLogic[$whereColumn] = $differentiatingValue;
                                $customLogic[$additionalWhereColumn] = $differentiatingSubValue;

                                // Insert for each where & additional where
                                DB::table($table)
                                    ->updateOrInsert($customLogic, $answersToInsert);
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Find a value in a collection related to an input source, with potentially extra columns to check
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @param  \App\Models\InputSource  $inputSource
     * @param  array  $extra
     *
     * @return mixed
     */
    public function searchCollectionForValue(Collection $collection, InputSource $inputSource, array $extra = [])
    {
        $search = $collection->where('input_source_id', $inputSource->id);

        foreach ($extra as $column => $value) {
            $search = $search->where($column, $value);
        }

        return $search->first();
    }
}
