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
use App\Helpers\Cooperation\Tool\FloorInsulationHelper;
use App\Helpers\Cooperation\Tool\InsulatedGlazingHelper;
use App\Helpers\Cooperation\Tool\RoofInsulationHelper;
use App\Helpers\Cooperation\Tool\SolarPanelHelper;
use App\Helpers\Cooperation\Tool\VentilationHelper;
use App\Helpers\Cooperation\Tool\WallInsulationHelper;
use App\Helpers\FileFormats\CsvHelper;
use App\Helpers\NumberFormatter;
use App\Helpers\ToolHelper;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\BuildingStatus;
use App\Models\InputSource;
use App\Models\Status;
use App\Models\Step;
use App\Models\SubSteppable;
use App\Models\ToolCalculationResult;
use App\Models\ToolLabel;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Traits\FluentCaller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DumpService
{
    const MODE_CSV = 'csv';
    const MODE_PDF = 'pdf';

    use FluentCaller;

    protected User $user;
    protected Building $building;
    protected InputSource $inputSource;

    protected bool $anonymize = false;
    protected string $mode = self::MODE_CSV;

    public array $headerStructure;

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
        $this->building = $user->building;
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
     * Set dump mode, which impacts how the dump is formed.
     *
     * @return $this
     */
    public function setMode(string $mode): self
    {
        $this->mode = $mode;
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
     * @return $this
     */
    public function createHeaderStructure(string $short): self
    {
        //TODO: See if we can use some dedicated keys so it's easier to remove these if not needed (as of right now,
        // in the PDF).
        if ($this->anonymize) {
            $headers = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.updated-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.status'),

                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.zip-code'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.city'),
            ];
        } else {
            $headers = [
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.created-at'),
                __('woningdossier.cooperation.admin.cooperation.reports.csv-columns.updated-at'),
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

        $structure = ToolHelper::getContentStructure($short, $this->mode);
        // If we should set the step prefix, we want to add the step name to each field
        if ($this->setStepPrefix()) {
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
     * @param bool $withConditionalLogic If we should follow conditional logic. Answers won't be shown if conditions
     *     don't match
     *
     * @return array
     */
    public function generateDump(bool $withConditionalLogic = true): array
    {
        $user = $this->user;
        $building = $this->building;
        $inputSource = $this->inputSource;

        $createdAt = optional($user->created_at)->format('Y-m-d');
        $updatedAt = $this->user->userActionPlanAdvices()
            ->forInputSource($inputSource)
            ->orderByDesc('updated_at')
            ->value('updated_at');
        $mostRecentStatus = $building->getMostRecentBuildingStatus();

        if (! $mostRecentStatus instanceof BuildingStatus) {
            Log::warning("Building status not set for building {$building->id}");
            DiscordNotifier::init()->notify("Building status not set for building {$building->id}");
            $mostRecentStatus = BuildingStatus::first();
            $buildingStatus = Status::findByShort('active')->name;
        } else {
            $buildingStatus = $mostRecentStatus->status->name;
        }

        $city = $building->city;
        $postalCode = $building->postal_code;

        if ($this->anonymize) {
            $data = [
                $createdAt, $updatedAt, $buildingStatus, $postalCode, $city,
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
                $createdAt, $updatedAt, $appointmentDate, $buildingStatus, $allowAccess, $connectedCoachNames,
                $firstName, $lastName, $email, $phoneNumber,
                $street, trim($number . ' ' . $extension), $postalCode, $city,
            ];
        }

        $calculateData = $this->getCalculateData();
        $conditionService = ConditionService::init()
            ->building($building)
            ->inputSource($inputSource);

        [$models, $answers] = $this->prepareHeaderStructure();

        foreach ($this->headerStructure as $key => $translation) {
            if (is_string(($key))) {
                $model = $models[$key];

                if ($model instanceof ToolQuestion) {
                    $processElement = true;
                    $humanReadableAnswer = null;

                    if ($withConditionalLogic) {
                        $processElement = $conditionService->forModel($model)->isViewable($answers);
                    }

                    if ($processElement) {
                        $humanReadableAnswer = ToolQuestionHelper::getHumanReadableAnswer(
                            $building,
                            $inputSource,
                            $model
                        );

                        // Priority slider situation
                        if (is_array($humanReadableAnswer) && $this->formatArrays()) {
                            $temp = '';
                            foreach ($humanReadableAnswer as $name => $answer) {
                                $temp .= "{$name}: {$answer}, ";
                            }
                            $humanReadableAnswer = substr($temp, 0, -2);
                        }
                    } elseif ($this->removeUnconditionals()) {
                        unset($this->headerStructure[$key]);
                        continue;
                    }

                    // So, by default the human readable answer is a mention of no answer being filled.
                    // However, this reads pretty gnarly so we nullify it.
                    if ($humanReadableAnswer === __('cooperation/frontend/tool.no-answer-given')) {
                        if (! $this->defaultEmptyAnswer()) {
                            $humanReadableAnswer = null;
                        }
                    } elseif ($this->withUnits() && ! empty($model->unit_of_measure)) {
                        $humanReadableAnswer .= " {$model->unit_of_measure}";
                    }

                    $data[$key] = $humanReadableAnswer;
                } elseif ($model instanceof ToolCalculationResult) {
                    $processElement = true;
                    $result = null;

                    if ($withConditionalLogic) {
                        $processElement = $conditionService->forModel($model)->isViewable($answers);
                    }

                    if ($processElement) {
                        $answer = Arr::get($calculateData, $model->short);
                        $result = $this->formatCalculation($key, $answer);

                        if ($this->withUnits() && ! empty($model->unit_of_measure)) {
                            $result .= " {$model->unit_of_measure}";
                        }
                    } elseif ($this->removeUnconditionals()) {
                        unset($this->headerStructure[$key]);
                        continue;
                    }

                    $data[$key] = $result;
                } elseif ($model instanceof ToolLabel) {
                    $processElement = true;
                    $label = null;

                    if ($withConditionalLogic) {
                        $processElement = $conditionService->forModel($model)->isViewable($answers);
                    }

                    if ($processElement) {
                        $label = $model->name;
                    } elseif ($this->removeUnconditionals()) {
                        unset($this->headerStructure[$key]);
                        continue;
                    }

                    $data[$key] = $label;
                }
            }
        }

        return $data;
    }

    protected function getCalculateData(): array
    {
        // TODO: When the other calculators are uniform, also call them via step short (so we can iterate);

        // collect some info about their building
        $user = $this->user;
        $building = $this->building;
        $inputSource = $this->inputSource;
        $userEnergyHabit = $user->energyHabit()->forInputSource($inputSource)->first();

        $calculations = [];
        $calculate = [
            'hr-boiler' => HighEfficiencyBoiler::class,
            'sun-boiler' => Heater::class,
            'heat-pump' => HeatPump::class,
        ];

        foreach ($calculate as $short => $calculator) {
            $calculations[$short] = $calculator::calculate($building, $inputSource);
        }

        $calculations['wall-insulation'] = WallInsulation::calculate($building, $inputSource, $userEnergyHabit,
            (new WallInsulationHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        $calculations['insulated-glazing'] = InsulatedGlazing::calculate($building, $inputSource, $userEnergyHabit,
            (new InsulatedGlazingHelper($user, $inputSource))
                ->createValues()
                ->getValues());

        $calculations['floor-insulation'] = FloorInsulation::calculate($building, $inputSource, $userEnergyHabit,
            (new FloorInsulationHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        $calculations['roof-insulation'] = RoofInsulation::calculate(
            $building,
            $inputSource,
            $userEnergyHabit,
            (new RoofInsulationHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        $calculations['solar-panels'] = SolarPanel::calculate(
            $building,
            (new SolarPanelHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        );

        $calculations['ventilation'] = Ventilation::calculate($building, $inputSource, $userEnergyHabit,
            (new VentilationHelper($user, $inputSource))
                ->createValues()
                ->getValues()
        )['result']['crack_sealing'];

        return $calculations;
    }

    protected function formatCalculation(string $key, $value)
    {
        $decimals = 0;
        $shouldRound = false;

        if (Str::contains($key, 'year') || ! is_numeric($value)) {
            return $value;
        }

        if (Str::contains($key, 'specs.size_collector') || Str::contains($key, 'interest_comparable')) {
            $decimals = 1;
        }

        if (Str::contains($key, 'percentage_consumption') || Str::contains($key, 'savings_')
            || (Str::contains($key, 'cost') && ! Str::contains( $key, 'roof-insulation'))) {
            $shouldRound = true;
        }

        return $this->formatOutput($key, $value, $decimals, $shouldRound);
    }

    /**
     * Format the output of the given column and value.
     *
     * @param string $column
     * @param mixed $value
     * @param int $decimals
     * @param bool $shouldRound
     *
     * @return string
     */
    protected function formatOutput(string $column, $value, int $decimals = 0, bool $shouldRound = false): string
    {
        if ($shouldRound) {
            $value = NumberFormatter::round($value);
        }

        // We should let Excel do the separation of thousands
        return number_format($value, $decimals, ',', '');
        //return NumberFormatter::format($value, $decimals, $shouldRound);
    }

    protected function prepareHeaderStructure(): array
    {
        $models = [];
        $modelIds = [];

        // Retrieve all models first
        foreach ($this->headerStructure as $key => $translation) {
            if (is_string(($key))) {
                // Structure is as follows:
                // 0: step shorts
                // 1: steppable short / calculation ref
                // n: potential calculation field
                $structure = explode('.', $key);

                $step = $structure[0];
                $potentialShort = $structure[1];
                if (Str::startsWith($potentialShort, 'question_')) {
                    $models[$key] = ToolQuestion::findByShort(Str::replaceFirst('question_', '', $potentialShort));
                } elseif (Str::startsWith($potentialShort, 'calculation_')) {
                    // The structure is built using the step as main short part for the calculation. We don't know how
                    // deeply nested the rest is, so we simply implode everything as that is also how its served.
                    $columnNest = implode('.', array_slice($structure, 2));

                    // We remove the calculation string, and append the column nest. Now we have the full short, which
                    // we can use to retrieve the model as well as to fetch from the array.
                    $column = Str::replaceFirst('calculation_', '', $potentialShort)
                        . (empty($columnNest) ? '' : ".{$columnNest}");

                    $models[$key] = ToolCalculationResult::findByShort($column);
                } elseif (Str::startsWith($potentialShort, 'label_')) {
                    $models[$key] = ToolLabel::findByShort(Str::replaceFirst('label_', '', $potentialShort));
                }
                $modelIds[] = $models[$key]->id;
            }
        }

        // Retrieve all conditions next
        $subSteppables = SubSteppable::whereIn('sub_steppable_type', [ToolQuestion::class, ToolCalculationResult::class])
            ->whereIn('sub_steppable_id', $modelIds)
            ->whereNotNull('sub_steppables.conditions')
            ->where('sub_steppables.conditions', '!=', DB::raw("cast('[]' as json)"))
            ->with([
                'subStep' => fn ($q) => $q->whereNotNull('sub_steps.conditions')
                    ->where('sub_steps.conditions', '!=', DB::raw("cast('[]' as json)"))
            ])
            ->get();

        // First fetch all conditions, so we can retrieve any required related answers in one go
        $conditions = $subSteppables->pluck('conditions')
            ->merge($subSteppables->pluck('subStep.*.conditions'))
            ->filter()
            ->flatten(1)
            ->all();

        $answers = ConditionEvaluator::init()
            ->inputSource($this->inputSource)
            ->building($this->building)
            ->getToolAnswersForConditions($conditions);

        // Don't use compact here, as we want to assign the return value to separate variables, and that only works
        // by using a basic index.
        return [$models, $answers];
    }

    private function withUnits(): bool
    {
        return $this->mode === self::MODE_PDF;
    }

    private function defaultEmptyAnswer(): bool
    {
        return $this->mode === self::MODE_PDF;
    }

    private function formatArrays(): bool
    {
        return $this->mode === self::MODE_CSV;
    }

    private function removeUnconditionals(): bool
    {
        return $this->mode === self::MODE_PDF;
    }

    private function setStepPrefix(): bool
    {
        return $this->mode === self::MODE_CSV;
    }
}
