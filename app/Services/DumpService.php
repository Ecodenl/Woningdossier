<?php

namespace App\Services;

use App\Calculations\FloorInsulation;
use App\Calculations\Heater;
use App\Calculations\HeatPump;
use App\Calculations\HighEfficiencyBoiler;
use App\Calculations\InsulatedGlazing;
use App\Calculations\RoofInsulation;
use App\Calculations\SolarPanel;
use App\Calculations\Ventilation;
use App\Calculations\WallInsulation;
use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\ConsiderableHelper;
use App\Helpers\Cooperation\Tool\FloorInsulationHelper;
use App\Helpers\Cooperation\Tool\HeaterHelper;
use App\Helpers\Cooperation\Tool\HighEfficiencyBoilerHelper;
use App\Helpers\Cooperation\Tool\InsulatedGlazingHelper;
use App\Helpers\Cooperation\Tool\RoofInsulationHelper;
use App\Helpers\Cooperation\Tool\SolarPanelHelper;
use App\Helpers\Cooperation\Tool\VentilationHelper;
use App\Helpers\Cooperation\Tool\WallInsulationHelper;
use App\Helpers\FileFormats\CsvHelper;
use App\Helpers\NumberFormatter;
use App\Helpers\ToolHelper;
use App\Helpers\ToolQuestionHelper;
use App\Helpers\Translation;
use App\Models\BuildingElement;
use App\Models\BuildingFeature;
use App\Models\buildingHeater;
use App\Models\BuildingHeating;
use App\Models\BuildingInsulatedGlazing;
use App\Models\BuildingPaintworkStatus;
use App\Models\BuildingPvPanel;
use App\Models\BuildingRoofType;
use App\Models\BuildingService;
use App\Models\BuildingVentilation;
use App\Models\Cooperation;
use App\Models\ElementValue;
use App\Models\EnergyLabel;
use App\Models\FacadeDamagedPaintwork;
use App\Models\FacadePlasteredSurface;
use App\Models\FacadeSurface;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\RoofTileStatus;
use App\Models\RoofType;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Models\ToolQuestionCustomValue;
use App\Models\User;
use App\Models\UserEnergyHabit;
use App\Traits\FluentCaller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class DumpService
{
    use FluentCaller;

    protected User $user;
    protected InputSource $inputSource;

    public array $headerStructure;
    public bool $anonymize = false;

    public function __construct()
    {
        $this->inputSource = InputSource::findByShort(InputSource::MASTER_SHORT);
    }

    /**
     * @param  User  $user
     *
     * @return $this
     */
    public function user(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @param  InputSource  $inputSource
     *
     * @return $this
     */
    public function inputSource(InputSource $inputSource): self
    {
        $this->inputSource = $inputSource;

        return $this;
    }

    /**
     * Anonymize the dump.
     *
     * @param  bool  $anonymize
     *
     * @return $this
     */
    public function anonymize(bool $anonymize = true): self
    {
        $this->anonymize = $anonymize;

        return $this;
    }

    /**
     * Set a header structure to re-use.
     *
     * @param  array  $headerStructure
     *
     * @return $this
     */
    public function setHeaderStructure(array $headerStructure): self
    {
        $this->headerStructure = Arr::dot($headerStructure);

        return $this;
    }

    /**
     * Create the header structure.
     *
     * @param  bool  $setStepPrefix
     *
     * @return $this
     */
    public function createHeaderStructure(bool $setStepPrefix = true): self
    {
        if ($this->anonymize) {
            $headers = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
            ];
        } else {
            $headers = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.coach-appointment-date'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.allow-access'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.associated-coaches'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.first-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.last-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.email'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.phonenumber'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.street'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.house-number'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
            ];
        }

        $structure = ToolHelper::getNewContentStructure();
        // If we should set the step prefix, we want to add the step name to each field
        if ($setStepPrefix) {
            foreach ($structure as $stepShort => $content) {
                $step = Step::findByShort($stepShort);

                if ($step instanceof Step) {
                    foreach (Arr::dot($content) as $dottedKey => $header) {
                        Arr::set($structure[$stepShort], $dottedKey, "{$step->name}: {$header}");
                    }
                }
            }
        }
        $structure = Arr::dot($structure);

        return $this->setHeaderStructure(array_merge($headers, $structure));
    }

    /**
     * Create a dump for the set header structure.
     *
     * @param  bool  $withConditionalLogic  If we should follow conditional logic. Answers won't be shown if conditions
     *     don't match
     *
     * @return array
     */
    public function generateDump(bool $withConditionalLogic = false): array
    {
        $user = $this->user;
        $building = $user->building;
        $inputSource = $this->inputSource;

        $createdAt = optional($user->created_at)->format('Y-m-d');
        $mostRecentStatus = $building->getMostRecentBuildingStatus();
        $buildingStatus = $mostRecentStatus->status->name;

        $city = $building->city;
        $postalCode = $building->postal_code;

        if ($this->anonymize) {
            $data = [
                $createdAt, $buildingStatus, $postalCode, $city,
            ];
        } else {
            $allowAccess = $user->allowedAccess() ? 'Ja' : 'Nee';
            $connectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuildingId($building->id);
            $connectedCoachNames = User::findMany($connectedCoaches->pluck('coach_id'))
                ->map(function ($user) {
                    return $user->getFullName();
                })->implode(', ');


            $firstName = $user->first_name;
            $lastName = $user->last_name;
            $email = $user->account->email;
            $phoneNumber = CsvHelper::escapeLeadingZero($user->phone_number);

            $street = $building->street;
            $number = $building->number;
            $extension = $building->extension ?? '';

            $appointmentDate = optional($mostRecentStatus->appointment_date)->format('Y-m-d');

            $data = [
                $createdAt, $appointmentDate, $buildingStatus, $allowAccess, $connectedCoachNames,
                $firstName, $lastName, $email, $phoneNumber,
                $street, trim($number . ' ' . $extension), $postalCode, $city,
            ];
        }

        $calculateData = $this->getNewCalculateData();
        $evaluator = ConditionEvaluator::init()
            ->building($building)
            ->inputSource($inputSource);

        foreach ($this->headerStructure as $key => $translation) {
            if (is_string(($key))) {
                // Structure is as follows:
                // 0: step shorts
                // 1: steppable short / save_in / calculation ref / considerables
                // n: potential calculation field / considerable struct
                $structure = explode('.', $key);

                $step = $structure[0];
                $potentialShort = $structure[1];
                if (Str::startsWith($potentialShort, 'question_')) {
                    $processAnswer = true;
                    $humanReadableAnswer = null;
                    $toolQuestion = ToolQuestion::findByShort(Str::replaceFirst('question_', '', $potentialShort));

                    // If we need to handle conditional logic, we basically check all sub steps and itself.
                    if ($withConditionalLogic) {
                        foreach ($toolQuestion->subSteps as $subStep) {
                            // TODO: Should it be an "OR" situation?
                            $processAnswer = $processAnswer && $evaluator->evaluate($subStep->conditions ?? []);
                        }

                        $processAnswer = $processAnswer && $evaluator->evaluate($toolQuestion->conditions ?? []);
                    }

                    if ($processAnswer) {
                        $humanReadableAnswer = ToolQuestionHelper::getHumanReadableAnswer($building, $inputSource,
                            $toolQuestion);
                        // Priority slider situation
                        if (is_array($humanReadableAnswer)) {
                            $temp = '';
                            foreach ($humanReadableAnswer as $name => $answer) {
                                $temp .= "{$name}: {$answer}, ";
                            }
                            $humanReadableAnswer = substr($temp, 0, -2);
                        }
                    }

                    $data[] = $humanReadableAnswer;
                } elseif (Str::startsWith($potentialShort, 'calculation_')) {
                    $columnNest = implode('.', array_slice($structure, 2));

                    $column = Str::replaceFirst('calculation_', '', $potentialShort)
                        . (empty($columnNest) ? '' : ".{$columnNest}");

                    $data[] = Arr::get($calculateData[$step], $column);
                } else {
                    if ($potentialShort === 'considerables') {
                        $considerableModel = $structure[2];
                        $considerableId = $structure[3];

                        // returns a bool, the values are keyed by 0 and 1
                        $considerable = $considerableModel::find($considerableId);
                        $considers = $user->considers($considerable, $inputSource);

                        $data[] = ConsiderableHelper::getConsiderableValues()[(int)$considers];
                    } else {
                        // Using the legacy notation, we will mimick getting the answer
                        $saveIn = ToolQuestionHelper::resolveSaveIn(Str::replaceFirst("{$step}.", '', $key),
                            $building);
                        $table  = $saveIn['table'];
                        $column = $saveIn['column'];
                        $where = $saveIn['where'];
                        $where['input_source_id'] = $inputSource->id;

                        $modelName = "App\\Models\\" . Str::studly(Str::singular($table));

                        $data[] = $modelName::allInputSources()->where($where)->get()->pluck($column)->first();
                    }
                }
            }
        }

        return $data;
    }

    protected function getNewCalculateData(): array
    {
        // TODO: When the calculators are uniform, instead call them via step short (so we can iterate);

        // collect some info about their building
        $user = $this->user;
        $building = $user->building;
        $inputSource = $this->inputSource;
        $userEnergyHabit = $user->energyHabit()->forInputSource($inputSource)->first();

        $wallInsulationSavings = WallInsulation::calculate($building, $inputSource, $userEnergyHabit,
            (new WallInsulationHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        $insulatedGlazingSavings = InsulatedGlazing::calculate($building, $inputSource, $userEnergyHabit,
            (new InsulatedGlazingHelper($user, $inputSource))
                ->createValues()
                ->getValues());

        $floorInsulationSavings = FloorInsulation::calculate($building, $inputSource, $userEnergyHabit,
            (new FloorInsulationHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        $roofInsulationSavings = RoofInsulation::calculate(
            $building,
            $inputSource,
            $userEnergyHabit,
            (new RoofInsulationHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        $highEfficiencyBoilerSavings = HighEfficiencyBoiler::calculate(
            $userEnergyHabit,
            (new HighEfficiencyBoilerHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        $solarPanelSavings = SolarPanel::calculate(
            $building,
            (new SolarPanelHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        $heaterSavings = Heater::calculate($building, $userEnergyHabit,
            (new HeaterHelper($user, $inputSource))
                ->createValues()
                ->getValues());

        $ventilationSavings = Ventilation::calculate($building, $inputSource, $userEnergyHabit,
            (new VentilationHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        $heatPumpSavings = HeatPump::calculate($building, $inputSource, $userEnergyHabit);

        return [
            'ventilation' => $ventilationSavings['result']['crack_sealing'],
            'wall-insulation' => $wallInsulationSavings,
            'insulated-glazing' => $insulatedGlazingSavings,
            'floor-insulation' => $floorInsulationSavings,
            'roof-insulation' => $roofInsulationSavings,
            'high-efficiency-boiler' => $highEfficiencyBoilerSavings,
            'solar-panels' => $solarPanelSavings,
            'heater' => $heaterSavings,
            'heat-pump' => $heatPumpSavings,
            'heating' => [
                'hr-boiler' => $highEfficiencyBoilerSavings,
                'sun-boiler' => $heaterSavings,
                'heat-pump' => $heatPumpSavings,
            ],
        ];
    }

    ## TODO: Replace legacy
    public static function makeHeaderText($stepName, $subStepName, $text)
    {
        // inside the content structure a step with no sub steps will be given a "-" as step
        // this way we can maintain nest
        if (empty($subStepName) || '-' == $subStepName) {
            $headerText = "{$stepName}: {$text}";
        } else {
            $headerText = "{$stepName}, {$subStepName}: {$text}";
        }

        return $headerText;
    }

    public static function getStructureForTotalDumpService(bool $anonymized, $prefixValuesWithStep = true)
    {
        if ($anonymized) {
            $headers = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.example-building'),
            ];
        } else {
            $headers = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.coach-appointment-date'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.allow-access'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.associated-coaches'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.first-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.last-name'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.email'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.phonenumber'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.street'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.house-number'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.building-type'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.build-year'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.example-building'),
            ];
        }

        // get the content structure of the whole tool.
        $structure = ToolHelper::getContentStructure();

        $leaveOutTheseDuplicates = [
            'general-data.building-characteristics.building_features.building_type_id',
            'general-data.building-characteristics.building_features.build_year',
            // hoofddak
            'roof-insulation.building_features.roof_type_id',
            // bewoners, gasverbruik en type ketel
            'high-efficiency-boiler.user_energy_habits.resident_count',
            'high-efficiency-boiler.user_energy_habits.amount_gas',
            'high-efficiency-boiler.service.5.service_value_id',
            // elektriciteitsverbruik
            'solar-panels.user_energy_habits.amount_electricity',
            // comfort niveau
            'heater.user_energy_habits.water_comfort_id',
            'heater.calculation.production_heat.help',
        ];

        // build the header structure, we will set those in the csv and use it later on to get the answers from the users.
        // unfortunately we cant array dot the structure since we only need the labels
        foreach ($structure as $stepShort => $stepStructure) {
            // building-detail contains data that is already present in the columns above
            $step = Step::findByShort($stepShort);
            foreach ($stepStructure as $subStep => $subStepStructure) {
                foreach ($subStepStructure as $tableWithColumnOrAndId => $contents) {
                    if ('calculations' == $tableWithColumnOrAndId) {
                        if ($prefixValuesWithStep) {
                            // If you want to go ahead and translate in a different namespace, do it here
                            // we will dot the array, map it so we can add the step name to it
                            $deeperContents = array_map(function ($content) use ($step, $subStep) {
                                return self::makeHeaderText($step->name, $subStep, $content);
                            }, Arr::dot($contents, $stepShort . '.' . $subStep . '.calculation.'));
                        } else {
                            $deeperContents = Arr::dot($contents, $stepShort . '.' . $subStep . '.calculation.');
                        }

                        $headers = array_merge($headers, $deeperContents);
                    } else {
                        $labelWithEuroNormalization = str_replace(['&euro;', '€'], ['euro', 'euro'], $contents['label']);

                        if ($prefixValuesWithStep) {
                            $subStepName = null;
                            if ('-' !== $subStep) {
                                $subStepName = optional(Step::findByShort($subStep))->name ?? '';
                            }

                            $headers[$stepShort . '.' . $subStep . '.' . $tableWithColumnOrAndId] = self::makeHeaderText($step->name ?? 'Algemeen', $subStepName, $labelWithEuroNormalization);
                        } else {
                            $headers[$stepShort . '.' . $subStep . '.' . $tableWithColumnOrAndId] = $labelWithEuroNormalization;
                        }
                    }
                }
            }
        }

        foreach ($leaveOutTheseDuplicates as $leaveOut) {
            unset($headers[$leaveOut]);
        }

        return $headers;
    }

    /**
     * Method to generate a total dump from a user for a specific input source.
     * This dump collects all possible data for a given user for the tool and returns it in an array.
     *
     * @param array $structureForTotalDump | we need the headers to get table and row data, provided from the self::dissectHeaders, using self::getStructureForTotalDumpService
     * @param Cooperation $cooperation ,
     * @param bool $withConditionalLogic | when true, it will return the data as happens in the dump. So if an input gets hidden it wont be put in the dump
     */
    public static function totalDump(array $structureForTotalDump, Cooperation $cooperation, User $user, InputSource $inputSource, bool $anonymized, bool $withTranslationsForColumns = true, bool $withConditionalLogic = false): array
    {
        $headers = $structureForTotalDump;
        $rows = [];

        if ($withTranslationsForColumns) {
            $rows['translations-for-columns'] = $headers;
        }

        // create a row where we will store the user data
        $row = [];

        // collect basic info from a user.
        $building = $user->building;
        $buildingId = $building->id;

        $createdAt = optional($user->created_at)->format('Y-m-d');
        $mostRecentStatus = $building->getMostRecentBuildingStatus();
        $buildingStatus = $mostRecentStatus->status->name;

        $allowAccess = $user->allowedAccess() ? 'Ja' : 'Nee';
        $connectedCoaches = BuildingCoachStatusService::getConnectedCoachesByBuildingId($building->id);
        $connectedCoachNames = User::findMany($connectedCoaches->pluck('coach_id'))
            ->map(function ($user) {
                return $user->getFullName();
            })->implode(', ');


        $firstName = $user->first_name;
        $lastName = $user->last_name;
        $email = $user->account->email;
        $phoneNumber = CsvHelper::escapeLeadingZero($user->phone_number);

        $street = $building->street;
        $number = $building->number;
        $extension = $building->extension ?? '';
        $city = $building->city;
        $postalCode = $building->postal_code;

        // get the building features from the resident
        $buildingFeature = $building->buildingFeatures;

        /** @var BuildingVentilation $buildingVentilation */
        $buildingVentilation = $building->buildingVentilations->first();

        $buildingType = $buildingFeature->buildingType->name ?? '';
        $buildYear = $buildingFeature->build_year ?? '';

        $exampleBuilding = '';
        if ($buildingFeature instanceof BuildingFeature) {
            $exampleBuilding = optional($buildingFeature->exampleBuilding)->isSpecific() ? $buildingFeature->exampleBuilding->name : '';
        }


        $appointmentDate = optional($mostRecentStatus->appointment_date)->format('Y-m-d');

        // set the personal userinfo
        if ($anonymized) {
            // set the personal userinfo
            $row[$building->id] = [
                $createdAt, $buildingStatus, $postalCode, $city,
                $buildingType, $buildYear, $exampleBuilding,
            ];
        } else {
            $row[$building->id] = [
                $createdAt, $appointmentDate, $buildingStatus, $allowAccess, $connectedCoachNames,
                $firstName, $lastName, $email, $phoneNumber,
                $street, trim($number . ' ' . $extension), $postalCode, $city,
                $buildingType, $buildYear, $exampleBuilding,
            ];
        }

        $calculateData = static::getCalculateData($user, $inputSource);

        // loop through the headers
        foreach ($headers as $tableWithColumnOrAndIdKey => $translatedInputName) {
            if (is_string($tableWithColumnOrAndIdKey)) {
                // explode it so we can do stuff with it.
                $tableWithColumnOrAndId = explode('.', $tableWithColumnOrAndIdKey);

                // collect some basic info
                // which will apply to (most) cases.
                $step = $tableWithColumnOrAndId[0];
                $subStep = $tableWithColumnOrAndId[1];
                $table = $tableWithColumnOrAndId[2];
                $columnOrId = $tableWithColumnOrAndId[3];

                $maybe1 = isset($tableWithColumnOrAndId[4]) ? $tableWithColumnOrAndId[4] : '';
                $maybe2 = isset($tableWithColumnOrAndId[5]) ? $tableWithColumnOrAndId[5] : '';
                //dump("Step: " . $step . " | table: " . $table . " | column or ID: " . $columnOrId . " | column: " . $maybe1 . " | costs or year: " . $maybe2);

                switch ($table) {
                    case 'building_ventilations':
                        $column = $columnOrId;
                        switch ($columnOrId) {
                            default:
                                $answer = null;
                                if ($buildingVentilation instanceof BuildingVentilation) {
                                    $optionsForQuestion = ToolHelper::getContentStructure($tableWithColumnOrAndIdKey)['options'];

                                    if (is_array($buildingVentilation->$column)) {
                                        $givenAnswers = array_flip($buildingVentilation->$column);

                                        $answer = implode(
                                            ', ',
                                            array_intersect_key($optionsForQuestion, $givenAnswers)
                                        );
                                    }
                                }
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $answer;
                                break;
                        }
                        break;

                    // handle the calculation table.
                    // No its not a table, but we treat it as in the structure array.
                    case 'calculation':
                        // works in most cases, otherwise they will be renamed etc.
                        $column = $columnOrId;
                        $costsOrYear = $tableWithColumnOrAndId[4] ?? null;

                        switch ($step) {
                            case 'roof-insulation':
                                $roofCategory = $tableWithColumnOrAndId[3];
                                $column = $tableWithColumnOrAndId[4];
                                $costsOrYear = $tableWithColumnOrAndId[5] ?? null;

                                $calculationResult = is_null($costsOrYear) ? $calculateData[$step][$subStep][$roofCategory][$column] ?? '' : $calculateData[$step][$subStep][$roofCategory][$column][$costsOrYear] ?? '';
                                break;
                            default:
                                $calculationResult = is_null($costsOrYear) ? $calculateData[$step][$subStep][$column] : $calculateData[$step][$subStep][$column][$costsOrYear] ?? '';
                                break;
                        }

                        $calculationResult = self::formatFieldOutput($column, $calculationResult, $maybe1, $maybe2);

//                        dd($calculationResult, $tableWithColumnOrAndIdKey, $column, $calculateData[$step][$subStep][$column]);
                        //dump("calculationResult: " . $calculationResult . " for step " . $step);

                        $row[$buildingId][$tableWithColumnOrAndIdKey] = $calculationResult ?? '';
                        break;

                    // handle the building_features table and its columns.
                    case 'building_features':
                        if ($buildingFeature instanceof BuildingFeature) {
                            switch ($columnOrId) {
                                case 'roof_type_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->roofType instanceof RoofType ? $buildingFeature->roofType->name : '';
                                    break;
                                case 'energy_label_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->energyLabel instanceof EnergyLabel ? $buildingFeature->energyLabel->name : '';
                                    break;
                                case 'facade_damaged_paintwork_id':
                                    $condition = 2 != $buildingFeature->facade_plastered_painted;
                                    if ($withConditionalLogic) {
                                        if ($condition) {
                                            $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->damagedPaintwork instanceof FacadeDamagedPaintwork ? $buildingFeature->damagedPaintwork->name : '';
                                        }
                                    } else {
                                        $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->damagedPaintwork instanceof FacadeDamagedPaintwork ? $buildingFeature->damagedPaintwork->name : '';
                                    }
                                    break;
                                case 'facade_plastered_painted':
                                    $possibleAnswers = [
                                        1 => \App\Helpers\Translation::translate('general.options.yes.title'),
                                        2 => \App\Helpers\Translation::translate('general.options.no.title'),
                                        3 => \App\Helpers\Translation::translate('general.options.unknown.title'),
                                    ];
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $possibleAnswers[$buildingFeature->facade_plastered_painted] ?? '';
                                    break;
                                case 'facade_plastered_surface_id':
                                    $condition = 2 != $buildingFeature->facade_plastered_painted;
                                    if ($withConditionalLogic) {
                                        if ($condition) {
                                            $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->plasteredSurface instanceof FacadePlasteredSurface ? $buildingFeature->plasteredSurface->name : '';
                                        }
                                    } else {
                                        $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->plasteredSurface instanceof FacadePlasteredSurface ? $buildingFeature->plasteredSurface->name : '';
                                    }
                                    break;
                                case 'monument':
                                    $possibleAnswers = [
                                        1 => \App\Helpers\Translation::translate('general.options.yes.title'),
                                        2 => \App\Helpers\Translation::translate('general.options.no.title'),
                                        0 => \App\Helpers\Translation::translate('general.options.unknown.title'),
                                    ];
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $possibleAnswers[$buildingFeature->monument] ?? '';
                                    break;
                                case 'cavity_wall':
                                    $possibleAnswers = [
                                        1 => \App\Helpers\Translation::translate('general.options.yes.title'),
                                        2 => \App\Helpers\Translation::translate('general.options.no.title'),
                                        0 => \App\Helpers\Translation::translate('general.options.unknown.title'),
                                    ];
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $possibleAnswers[$buildingFeature->cavity_wall] ?? '';
                                    break;
                                case 'wall_joints':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->wallJoints instanceof FacadeSurface ? $buildingFeature->wallJoints->name : '';
                                    break;
                                case 'contaminated_wall_joints':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->contaminatedWallJoints instanceof FacadeSurface ? $buildingFeature->contaminatedWallJoints->name : '';
                                    break;
                                case 'window_surface':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = NumberFormatter::format($buildingFeature->$columnOrId, 2) ?? '';
                                    break;
                                default:
                                    // the column does not need a relationship, so just get the column
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingFeature->$columnOrId ?? '';
                                    break;
                            }
                        } else {
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                        break;

                    // handle the building_roof_types table and its columns.
                    case 'building_roof_types':
                        $roofTypeId = $columnOrId;
                        //$column     = $tableWithColumnOrAndId[3];
                        $column = $maybe1;

                        $buildingRoofType = $building->roofTypes
                            ->where('roof_type_id', $roofTypeId)
                            ->first();

                        if ($buildingRoofType instanceof BuildingRoofType) {
                            switch ($column) {
                                case 'element_value_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->elementValue instanceof ElementValue ? $buildingRoofType->elementValue->value : '';
                                    break;
                                case 'building_heating_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->buildingHeating instanceof BuildingHeating ? $buildingRoofType->buildingHeating->name : '';
                                    break;
                                default:
                                    // check if we need to get data from the extra column
                                    if (stristr($tableWithColumnOrAndIdKey, 'extra')) {
                                        $extraKey = explode('extra.', $tableWithColumnOrAndIdKey)[1];
                                        if (in_array($extraKey, ['tiles_condition', 'measure_application_id'])) {
                                            $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->extra[$extraKey] ?? '';

                                            if (!empty($buildingRoofType->extra[$extraKey]) && 'tiles_condition' == $extraKey) {
                                                $status = RoofTileStatus::find((int)$row[$buildingId][$tableWithColumnOrAndIdKey]);
                                                $row[$buildingId][$tableWithColumnOrAndIdKey] = ($status instanceof RoofTileStatus) ? $status->name : '';
                                            }
                                            // The measure application id, in this case. can be 0, this means the option: "niet" has been chosen the option is not saved as a measure application
                                            if ('measure_application_id' == $extraKey) {
                                                $measureApplication = MeasureApplication::find((int)$row[$buildingId][$tableWithColumnOrAndIdKey]);
                                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $measureApplication instanceof MeasureApplication ? $measureApplication->measure_name : __('roof-insulation.measure-application.no.title');
                                            }
                                        } else {
                                            // literal
                                            $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->extra[$extraKey] ?? '';
                                        }
                                    } else {
                                        $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingRoofType->$column ?? '';
                                    }
                                    break;
                            }
                        } else {
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                        break;

                    // handle the user_interest table and its columns.
                    case 'considerables':
                        $considerableModel = $tableWithColumnOrAndId[3];
                        $considerableId = $tableWithColumnOrAndId[4];

                        // returns a bool, the values are keyed by 0 and 1
                        $considerable = $considerableModel::find($considerableId);
                        $considers = $user->considers($considerable, $inputSource);

                        $row[$buildingId][$tableWithColumnOrAndIdKey] = ConsiderableHelper::getConsiderableValues()[(int)$considers];
                        break;

                    // handle the element table.
                    case 'element':
                        $elementOrServiceId = $columnOrId;

                        /** @var BuildingElement $element */
                        $buildingElement = $building->buildingElements
                            ->where('element_id', $elementOrServiceId)
                            ->first();

                        if ($buildingElement instanceof BuildingElement) {
                            // check if we need to get data from the extra column
                            if (stristr($tableWithColumnOrAndIdKey, 'extra')) {
                                $extraKey = explode('extra.', $tableWithColumnOrAndIdKey)[1];

                                $row[$buildingId][$tableWithColumnOrAndIdKey] = is_array($buildingElement->extra) ? self::translateExtraValueIfNeeded($buildingElement->extra[$extraKey]) ?? '' : '';
                            } else {
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingElement->elementValue->value ?? '';
                            }
                        } else {
                            // always set defaults
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                        break;

                    // handle the service table.
                    case 'service':
                        $elementOrServiceId = $columnOrId;

                        $buildingService = $building->buildingServices
                            ->where('service_id', $elementOrServiceId)
                            ->first();

                        if ($buildingService instanceof BuildingService) {
                            // check if we need to get data from the extra column
                            if (stristr($tableWithColumnOrAndIdKey, 'extra')) {
                                $extraKey = explode('extra.', $tableWithColumnOrAndIdKey)[1];
                                $extraIsArray = is_array($buildingService->extra);

                                // if is array, try to get the answer from the extra column, does the key not exist set a default value.
                                $answer = $extraIsArray ? optional($buildingService->extra)[$extraKey] : null;

                                // when the answer is a bool / true its checked, so instead of showing true we show ja.
                                // total sun panels is stored in same column, but need to be treated as a number
                                if ('true' == $answer && 'total-sun-panels' !== $buildingService->service->short) {
                                    $answer = 'Ja';
                                } elseif ('total-sun-panels' !== $buildingService->service->short && 'false' == $answer) {
                                    $answer = 'Nee';
                                }

                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $answer;
                            } else {
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingService->serviceValue->value ?? '';
                            }
                        } else {
                            // always set defaults
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                        break;

                    // handle the building_insulated_glazing table and its columns.
                    case 'building_insulated_glazings':
                        $measureApplicationId = $columnOrId;
                        $column = $tableWithColumnOrAndId[4];

                        /** @var BuildingInsulatedGlazing $buildingInsulatedGlazing */
                        $buildingInsulatedGlazing = $building->currentInsulatedGlazing
                            ->where('measure_application_id', $measureApplicationId)
                            ->first();

                        if ($buildingInsulatedGlazing instanceof BuildingInsulatedGlazing) {
                            switch ($column) {
                                case 'insulating_glazing_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingInsulatedGlazing->insulatedGlazing->name ?? '';
                                    break;
                                case 'building_heating_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingInsulatedGlazing->buildingHeating->name ?? '';
                                    break;
                                default:
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingInsulatedGlazing->$column ?? '';
                                    break;
                            }
                        } else {
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                        break;

                    // handle the building_pv_panels table and its column
                    case 'building_pv_panels':
                        $column = $columnOrId;

                        /** @var BuildingPvPanel $buildingPvPanel */
                        $buildingPvPanel = $building->pvPanels;

                        if ($buildingPvPanel instanceof BuildingPvPanel) {
                            switch ($column) {
                                case 'pv_panel_orientation_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingPvPanel->orientation->name ?? '';
                                    break;
                                default:
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingPvPanel->$column ?? '';
                                    break;
                            }
                        } else {
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                        break;

                    // handle the building_heaters table and its column
                    case 'building_heaters':
                        $column = $columnOrId;

                        /** @var buildingHeater $buildingHeater */
                        $buildingHeater = $building->heater;

                        if ($buildingHeater instanceof BuildingHeater) {
                            switch ($column) {
                                case 'pv_panel_orientation_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingHeater->orientation->name ?? '';
                                    break;
                                default:
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingHeater->$column ?? '';
                                    break;
                            }
                        } else {
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                        break;

                    case 'tool_question_answers':
                        $column = $columnOrId;
                        switch ($column) {
                            case 'cook-type':
                                $cookType = $building->getAnswer($inputSource, ToolQuestion::findByShort('cook-type'));
                                $answer = __('woningdossier.cooperation.radiobutton.no');
                                if ($cookType == "gas") {
                                    $answer = __('woningdossier.cooperation.radiobutton.yes');
                                }
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $answer ?? '';
                                break;
                            default:
                                $answer = "";
                                $answers = $building->getAnswer($inputSource, ToolQuestion::findByShort($column));

                                // this will allow us to translates the answers
                                // only when its a array, it will be null when no answer is given.
                                if (is_array($answers)) {
                                    $answer = ToolQuestionCustomValue::whereIn('short', $answers)
                                            ->get()
                                            ->pluck('name')
                                            ->implode(', ') ?? '';
                                }
                                $row[$buildingId][$tableWithColumnOrAndIdKey] = $answer;

                                break;
                        }
                        break;

                    // handle the user_energy_habits table and its column
                    case 'user_energy_habits':
                        $column = $columnOrId;

                        /** @var UserEnergyHabit $userEnergyHabit */
                        $userEnergyHabit = $user->energyHabit;

                        if ($userEnergyHabit instanceof UserEnergyHabit) {
                            switch ($column) {
                                case 'renovation_plans':
                                    $renovationPlanAnswerOptions = [
                                        1 => __('cooperation/tool/general-data/interest.index.motivation.renovation-plans.options.yes-within-2-year'),
                                        2 => __('cooperation/tool/general-data/interest.index.motivation.renovation-plans.options.yes-within-5-year'),
                                        0 => __('cooperation/tool/general-data/interest.index.motivation.renovation-plans.options.none'),
                                    ];
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $renovationPlanAnswerOptions[$userEnergyHabit->renovation_plans] ?? null;
                                    break;
                                case 'water_comfort_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = optional($userEnergyHabit->comfortLevelTapWater)->name;
                                    break;
                                case 'heating_first_floor':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = optional($userEnergyHabit->heatingFirstFloor)->name;
                                    break;
                                case 'heating_second_floor':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = optional($userEnergyHabit->heatingSecondFloor)->name ?? '';
                                    break;
                                default:
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $userEnergyHabit->$column ?? '';
                                    break;
                            }
                        } else {
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                        break;

                    // handle the building_paintwork_statuses table and its column
                    case 'building_paintwork_statuses':
                        $column = $columnOrId;

                        /** @var BuildingPaintworkStatus $buildingPaintworkStatus */
                        $buildingPaintworkStatus = $building->currentPaintworkStatus;

                        if ($buildingPaintworkStatus instanceof BuildingPaintworkStatus) {
                            switch ($column) {
                                case 'paintwork_status_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingPaintworkStatus->paintworkStatus->name ?? '';
                                    break;
                                case 'wood_rot_status_id':
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingPaintworkStatus->woodRotStatus->name ?? '';
                                    break;
                                default:
                                    $row[$buildingId][$tableWithColumnOrAndIdKey] = $buildingPaintworkStatus->$column ?? '';
                                    break;
                            }
                        } else {
                            $row[$buildingId][$tableWithColumnOrAndIdKey] = '';
                        }
                        break;
                }
            }
        }

        $rows['user-data'] = $row[$buildingId];
        $rows['calculations'] = $calculateData;

        return $rows;
    }

    /**
     * Return the calculate data for each step, for a user, with its given inputsource.
     */
    public static function getCalculateData(User $user, InputSource $inputSource): array
    {
        // collect some info about their building
        $building = $user->building;

        $userEnergyHabit = $user->energyHabit;

        $wallInsulationSavings = WallInsulation::calculate($building, $inputSource, $userEnergyHabit,
            (new WallInsulationHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        $insulatedGlazingSavings = InsulatedGlazing::calculate($building, $inputSource, $userEnergyHabit,
            (new InsulatedGlazingHelper($user, $inputSource))
                ->createValues()
                ->getValues());

        $floorInsulationSavings = FloorInsulation::calculate($building, $inputSource, $userEnergyHabit,
            (new FloorInsulationHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        $roofInsulationSavings = RoofInsulation::calculate(
            $building,
            $inputSource,
            $userEnergyHabit,
            (new RoofInsulationHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        $highEfficiencyBoilerSavings = HighEfficiencyBoiler::calculate(
            $userEnergyHabit,
            (new HighEfficiencyBoilerHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        $solarPanelSavings = SolarPanel::calculate(
            $building,
            (new SolarPanelHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        $heaterSavings = Heater::calculate($building, $userEnergyHabit,
            (new HeaterHelper($user, $inputSource))
                ->createValues()
                ->getValues());

        $ventilationSavings = Ventilation::calculate($building, $inputSource, $userEnergyHabit,
            (new VentilationHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        return [
            'ventilation' => [
                // for now, in the future this may change and multiple results can be returned
                '-' => $ventilationSavings['result']['crack_sealing'],
            ],
            'wall-insulation' => [
                '-' => $wallInsulationSavings,
            ],
            'insulated-glazing' => [
                '-' => $insulatedGlazingSavings,
            ],
            'floor-insulation' => [
                '-' => $floorInsulationSavings,
            ],
            'roof-insulation' => [
                '-' => $roofInsulationSavings,
            ],
            'high-efficiency-boiler' => [
                '-' => $highEfficiencyBoilerSavings,
            ],
            'solar-panels' => [
                '-' => $solarPanelSavings,
            ],
            'heater' => [
                '-' => $heaterSavings,
            ],
        ];
    }

    protected static function formatFieldOutput($column, $value, $maybe1, $maybe2)
    {
        //dump("formatFieldOutput (" . $column . ", " . $value . ", " . $maybe1 . ", " . $maybe2 . ")");
        $decimals = 0;
        $shouldRound = false;

        if (self::isYear($column) || self::isYear($maybe1, $maybe2)) {
            return $value;
        }

        if (!is_numeric($value)) {
            return $value;
        }

        if (in_array($column, ['interest_comparable'])) {
            $decimals = 1;
        }
        if ('specs' == $column && 'size_collector' == $maybe1) {
            $decimals = 1;
        }
        if ('paintwork' == $column && 'costs' == $maybe1) {
            /// round the cost for paintwork
            $shouldRound = true;
        }

        return self::formatOutput($column, $value, $decimals, $shouldRound);
    }

    /**
     * Format the output of the given column and value.
     *
     * @param string $column
     * @param mixed $value
     * @param int $decimals
     * @param bool $shouldRound
     *
     * @return float|int|string
     */
    protected static function formatOutput($column, $value, $decimals = 0, $shouldRound = false)
    {
        //dump("formatOutput (" . $column . ", " . $value . ", " . $decimals . ", " . $shouldRound . ")");

        if (in_array($column, ['percentage_consumption']) ||
            false !== stristr($column, 'savings_') ||
            stristr($column, 'cost')) {
            $value = NumberFormatter::round($value);
        }
        if ($shouldRound) {
            $value = NumberFormatter::round($value);
        }
        // We should let Excel do the separation of thousands
        return number_format($value, $decimals, ',', '');
        //return NumberFormatter::format($value, $decimals, $shouldRound);
    }

    protected static function translateExtraValueIfNeeded($value)
    {
        if (in_array($value, ['yes', 'no', 'unknown'])) {
            $key = 'general.options.%s.title';

            return Translation::translate(sprintf($key, $value));
        }
    }

    /**
     * Returns whether or not two (optional!) columns contain a year or not.
     *
     * @param string $column
     * @param string $extraValue
     *
     * @return bool
     */
    protected static function isYear($column, $extraValue = '')
    {
        if (!is_null($column)) {
            if (false !== stristr($column, 'year')) {
                return true;
            }
            if ('extra' == $column) {
                return in_array($extraValue, [
                    'year',
                ]);
            }
        }

        return false;
    }
}
