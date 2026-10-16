<?php

namespace App\Services\SmartTwin;

use App\Helpers\Conditions\ConditionEvaluator;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\BuildingFeature;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\SubStep;
use App\Models\ToolQuestion;
use App\Models\UserActionPlanAdvice;
use App\Services\SmartTwin\Mapping\Elements\Element;
use App\Services\SmartTwin\Mapping\Elements\ElementScope;
use App\Services\SmartTwin\Mapping\FilledQuestions;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * What "Gegevens woning bekijken" shows: the dwelling as it is now, after SmartTwin's last import
 * (decision 2026-10-15-smarttwin-gegevens-woning-pagina).
 *
 * The four parts the process design names, "algemeen, energielabel, gebruik en woningstatus", are
 * the quick scan steps SmartTwin takes over. Everything is read from the dossier (master), the
 * source the rest of Hoomdossier and the report use; SmartTwin's import is what filled it.
 *
 * - General: building type, build year and surface (SmartTwin's Building Data), and whether the
 *   dwelling is rented or owned and a monument, where the dossier holds those. SmartTwin does not
 *   deliver them; they are to be asked up front, at registration (decision
 *   2026-10-15-smarttwin-rapport-na-omslagpunt, P5).
 * - Energy label: the registered one, and SmartTwin's calculation of the dwelling as it is.
 * - State: per part of the dwelling, the questions SmartTwin fills (FilledQuestions), with the
 *   measures marked as carried out on that part. SmartTwin's figures stay under them as SmartTwin's,
 *   with the date of its calculation; nothing is recalculated here (S6d).
 * - Usage: the answers that are there; SmartTwin does not deliver usage (B7).
 */
final class HouseData
{
    /** The page groups the crawlspace with the floor, which is where the dossier asks about it. */
    /** The general questions the dossier asks itself rather than SmartTwin. */
    public const GENERAL_QUESTIONS = ['building-contract-type', 'monument'];

    private const PART_OF_ELEMENT = [
        'facade'     => 'facade',
        'floor'      => 'floor',
        'crawlspace' => 'floor',
        'roof'       => 'roof',
    ];

    /**
     * @param  array<int, array{label: string, answer: string|array<string, mixed>}>  $general
     * @param  array<string, array{rows: array<int, array{label: string, answer: string|array<string, mixed>}>, executed: array<int, array{measure: string, date: Carbon, description: ?string}>}>  $parts
     * @param  array<int, array{label: string, answer: string|array<string, mixed>}>  $usage
     */
    public function __construct(
        public readonly ?BuildingFeature $features,
        public readonly ?string $estimatedEnergyLabel,
        public readonly ?Carbon $calculatedAt,
        public readonly array $general,
        public readonly array $parts,
        public readonly array $usage,
    )
    {
    }

    public static function for(Building $building): self
    {
        $master = InputSource::master();
        $package = $building->smartTwinPackage;

        $features = $building->buildingFeatures()
            ->allInputSources()
            ->forInputSource($master)
            ->with(['buildingType', 'buildingTypeCategory', 'energyLabel'])
            ->first();

        return new self(
            $features instanceof BuildingFeature ? $features : null,
            $package?->current_result['energyLabel'] ?? null,
            $package?->imported_at,
            self::rows($building, $master, self::GENERAL_QUESTIONS),
            self::parts($building, $master),
            self::usage($building, $master),
        );
    }

    /**
     * @return array<string, array{rows: array<int, array{label: string, answer: string|array<string, mixed>}>, executed: array<int, array{measure: string, date: Carbon, description: ?string}>}>
     */
    private static function parts(Building $building, InputSource $master): array
    {
        $executed = self::executedByPart($building, $master);
        $parts = [];

        foreach (FilledQuestions::BY_PART as $part => $shorts) {
            $rows = [];

            foreach ($shorts as $short) {
                $toolQuestion = ToolQuestion::findByShort($short);

                if ($toolQuestion instanceof ToolQuestion) {
                    $row = self::row($building, $master, $toolQuestion);

                    if (! is_null($row)) {
                        $rows[] = $row;
                    }
                }
            }

            if (! empty($rows) || ! empty($executed[$part])) {
                $parts[$part] = ['rows' => $rows, 'executed' => $executed[$part] ?? []];
            }
        }

        return $parts;
    }

    /**
     * The measures marked as carried out, by the part of the dwelling they work on. A measure's part
     * follows from its step (ElementScope), as for SmartTwin's advice. An own measure has no step,
     * and so no part on this page.
     *
     * @return array<string, array<int, array{measure: string, date: Carbon, description: ?string}>>
     */
    private static function executedByPart(Building $building, InputSource $master): array
    {
        $byPart = [];

        $advices = UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $building->user_id)
            ->where('input_source_id', $master->id)
            ->where('user_action_plan_advisable_type', MeasureApplication::class)
            ->whereNotNull('executed_at')
            ->orderBy('executed_at')
            ->get();

        foreach ($advices as $advice) {
            $measure = MeasureApplication::find($advice->user_action_plan_advisable_id);

            if (! $measure instanceof MeasureApplication) {
                continue;
            }

            $parts = [];
            foreach (ElementScope::forMeasure($measure->short, $measure->step->short) as $scope) {
                $part = self::PART_OF_ELEMENT[$scope->element->value] ?? null;

                if (! is_null($part)) {
                    $parts[$part] = true;
                }
            }

            foreach (array_keys($parts) as $part) {
                $byPart[$part][] = [
                    'measure'     => $measure->getTranslation('measure_name', app()->getLocale()),
                    'date'        => Carbon::parse($advice->executed_at),
                    'description' => $advice->executed_description,
                ];
            }
        }

        return $byPart;
    }

    /**
     * The answers of the quick scan's usage step that are there, in the order it asks them, leaving
     * out what its conditions no longer ask and the comments.
     *
     * @return array<int, array{label: string, answer: string|array<string, mixed>}>
     */
    private static function usage(Building $building, InputSource $master): array
    {
        $step = Step::findByShort('usage-quick-scan');

        if (! $step instanceof Step) {
            return [];
        }

        $subSteps = $step->subSteps()
            ->whereHas('subStepTemplate', fn ($query) => $query->where('short', '!=', 'template-summary'))
            ->with(['subSteppables'])
            ->orderBy('order')
            ->get();

        $conditions = $subSteps->pluck('conditions')
            ->merge($subSteps->pluck('subSteppables.*.conditions')->flatten(1))
            ->filter()
            ->flatten(1)
            ->all();

        $evaluator = ConditionEvaluator::init()->building($building)->inputSource($master);
        $answers = $evaluator->getToolAnswersForConditions($conditions);

        $rows = [];

        /** @var SubStep $subStep */
        foreach ($subSteps as $subStep) {
            if (! $evaluator->setAnswers($answers)->evaluate($subStep->conditions ?? [])) {
                continue;
            }

            foreach ($subStep->subSteppables->where('sub_steppable_type', ToolQuestion::class) as $pivot) {
                $toolQuestion = $pivot->subSteppable;

                if (! $toolQuestion instanceof ToolQuestion || Str::contains($toolQuestion->short, 'comment')) {
                    continue;
                }

                if (! empty($pivot->conditions) && ! $evaluator->setAnswers($answers)->evaluate($pivot->conditions)) {
                    continue;
                }

                $row = self::row($building, $master, $toolQuestion);

                if (! is_null($row)) {
                    $rows[] = $row;
                }
            }
        }

        return $rows;
    }

    /**
     * The questions that have an answer, in the order given.
     *
     * @param  array<int, string>  $shorts
     * @return array<int, array{label: string, answer: string|array<string, mixed>}>
     */
    private static function rows(Building $building, InputSource $master, array $shorts): array
    {
        $rows = [];

        foreach ($shorts as $short) {
            $toolQuestion = ToolQuestion::findByShort($short);
            $row = $toolQuestion instanceof ToolQuestion ? self::row($building, $master, $toolQuestion) : null;

            if (! is_null($row)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * A question and its answer as the dossier's summaries show it, or null when it has none.
     *
     * @return null|array{label: string, answer: string|array<string, mixed>}
     */
    private static function row(Building $building, InputSource $master, ToolQuestion $toolQuestion): ?array
    {
        $answer = $building->getAnswer($master, $toolQuestion);

        if ((empty($answer) && ! is_numeric($answer)) || (is_array($answer) && empty(array_filter($answer, fn ($value) => ! is_null($value))))) {
            return null;
        }

        $toolQuestion = ToolQuestionHelper::handleToolQuestionReplaceables($building, $master, $toolQuestion);

        return [
            'label'  => Str::ucfirst(trim($toolQuestion->getTranslation('name', app()->getLocale()))),
            'answer' => ToolQuestionHelper::getHumanReadableAnswer($building, $master, $toolQuestion, false, $answer),
        ];
    }
}
