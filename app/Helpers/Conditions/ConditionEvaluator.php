<?php

namespace App\Helpers\Conditions;

use App\Models\Building;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Traits\FluentCaller;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ConditionEvaluator
{
    use FluentCaller;

    protected Building $building;
    protected InputSource $inputSource;
    protected bool $explain = false;

    public ?Collection $answers = null;
    protected array $customResults = [];

    public function building(Building $building): self
    {
        $this->building = $building;
        return $this;
    }

    public function inputSource(InputSource $inputSource): self
    {
        $this->inputSource = $inputSource;
        return $this;
    }

    public function explain(): self
    {
        $this->explain = true;
        return $this;
    }

    public function setAnswers(Collection $answers): self
    {
        $this->answers = $answers;
        return $this;
    }

    public function getToolAnswersForConditions(array $conditions, ?Collection $answers = null): Collection
    {
        $answers = $answers instanceof Collection ? $answers : collect();
        $ignore = $answers->keys()->all();

        // Pass clauses reference other models with conditions, so we need to resolve them.
        $passClauses = $this->fetchPassClauses($conditions);

        $clauseConditions = [];
        foreach ($passClauses as $clause) {
            $clauseConditions[] = $this->fetchPassConditions($clause['column'], $clause['value'], true);
        }

        // Get answers for condition columns, but ensure we don't fetch special evaluators, as they don't have
        // answers. We will merge the earlier fetched pass clause keys. Due to the fact we have AND constructs
        // that contain separate OR clauses, we need to flatten twice, both for single and double level.
        $questionKeys = collect(Arr::flatten($conditions, 2))
            ->merge(collect(Arr::flatten($conditions, 1)))
            ->merge(collect(Arr::flatten($clauseConditions, 2)))
            ->merge(collect(Arr::flatten($clauseConditions, 1)))
            ->whereNotIn('operator', [Clause::PASSES, Clause::NOT_PASSES])
            ->where('column', '!=', 'fn')
            ->whereNotIn('column', $ignore)
            ->pluck('column')
            ->unique()
            ->filter()
            ->values();

        // the structure of the questionKeys tells us how to retrieve the answer
        // if it contains a dot, it's in a table.column format
        // if not, it's a tool question short
        $collectedAnswers = [];
        foreach ($questionKeys as $questionKey) {
            if (Str::contains($questionKey, '.')) {
                // table.column
                $dbParts = explode('.', $questionKey);
                if (count($dbParts) <= 1) {
                    $answer = null;
                } else {
                    $table = array_shift($dbParts);
                    $column = array_shift($dbParts);
                    $row = DB::table($table)->where(
                        'building_id',
                        '=',
                        $this->building->id
                    )
                        ->where(
                            'input_source_id',
                            '=',
                            $this->inputSource->id
                        )->first();
                    $answer = $row->$column ?? null;
                }
            } else {
                // tool question short
                $toolQuestion = ToolQuestion::findByShort($questionKey);
                if (! $toolQuestion instanceof ToolQuestion) {
                    continue; // just skip this.
                }
                // in case of checkbox $answer is array
                // else plain value
                $answer = $this->building->getAnswer(
                    $this->inputSource,
                    $toolQuestion
                );
            }
            $collectedAnswers[$questionKey] = $answer;
        }

        return collect($collectedAnswers)->merge($answers);
    }

    public function evaluate(array $conditions): bool
    {
        // Set answers if needed.
        if (is_null($this->answers)) {
            $this->setAnswers($this->getToolAnswersForConditions($conditions));
        }

        return $this->evaluateCollection($conditions);
    }

    protected function evaluateCollection(array $conditions): bool
    {
        $result = false;
        foreach ($conditions as $andClause) {
            $result = $result || $this->evaluateAnd($andClause, $this->answers);
        }

        return empty($conditions) || $result;
    }

    protected function evaluateAnd(array $clauses, Collection $collection): bool
    {
        $result = true;

        if ($this->explain) {
            Log::debug("evaluateAnd EXPLAIN Before: Result is " . ($result ? "true" : "false"));
        }

        foreach ($clauses as $clause) {
            $subResult = false;
            if (array_key_exists('column', $clause)) {
                $subResult = $this->evaluateClause($clause, $collection);
            } else {
                foreach ($clause as $subClause) {
                    $subResult = $subResult || $this->evaluateClause($subClause, $collection);
                }
            }

            $result = $result && $subResult;
            if ($this->explain) {
                Log::debug("evaluateAnd EXPLAIN Between: Result is " . ($result ? "true" : "false"));
            }
        }

        if ($this->explain) {
            Log::debug("evaluateAnd EXPLAIN After: Result is " . ($result ? "true" : "false"));
        }

        return $result;
    }

    protected function evaluateClause(array $clause, Collection $collection): bool
    {
        extract($clause);
        $operator = $operator ?? '';
        /**
         * @var string $column
         * @var string $operator
         * @var $value
         */

        if ($this->explain) {
            $v = $value ?? "No value";
            if (is_array($v)) {
                $v = json_encode($v);
            }

            if ($this->explain) {
                Log::debug(
                    "evaluateClause EXPLAIN: " . sprintf(
                        '%s %s %s',
                        is_string($column) ? $column : json_encode($column),
                        $operator,
                        $v
                    )
                );
                Log::debug("evaluateClause EXPLAIN against");
                Log::debug($collection);
            }
        }

        // first check if its a custom evaluator
        if ($column == "fn") {
            $customEvaluatorClass = "App\Helpers\Conditions\Evaluators\\{$operator}";
            return $this->handleCustomEvaluator($customEvaluatorClass, ($value ?? null), $collection);
        }

        // Else check if we should do sub-evaluation
        if ($operator === Clause::PASSES || $operator === Clause::NOT_PASSES) {
            $conditions = $this->fetchPassConditions($column, $value);

            if (! empty($conditions)) {
                $answersForNewConditions = $this->getToolAnswersForConditions($conditions, $collection);
                $answers = $this->answers instanceof Collection ? $this->answers : collect();

                $this->setAnswers(
                    $answers->merge($answersForNewConditions)
                );

                // Return result based on whether it should or should not pass
                $result = $this->evaluate($conditions);
                return $operator === Clause::PASSES ? $result : ! $result;
            }

            // Empty conditions are always true
            return true;
        }

        // Else fall through to other clauses
        if (! $collection->has($column)) {
            return false;
        }
        $values = $collection->get($column);

        if (empty($operator)) {
            $operator = Clause::CONTAINS;
        }

        if ($operator == Clause::CONTAINS || $operator == Clause::NOT_CONTAINS) {
            if ($values instanceof Collection) {
                // as this is just a list with integer indexes, we have to do
                // array comparison. Therefore:
                $values = $values->all();
                // and fallthrough
            }
            if (is_array($values)) {
                $result = in_array($value, $values);
                return $operator == Clause::CONTAINS ? $result : ! $result;
            }

            // values is plain value
            $result = $values == $value;
            return $operator == Clause::CONTAINS ? $result : ! $result;
        }

        // values will *probably* (should...) be containing a single value
        switch ($operator) {
            case Clause::GT:
                return $values > $value;
            case Clause::GTE:
                return $values >= $value;
            case Clause::LT:
                return $values < $value;
            case Clause::LTE:
                return $values <= $value;
            case Clause::NEQ:
                return $values != $value;
            case Clause::EQ:
            default:
                return $values == $value;
        }
    }

    protected function handleCustomEvaluator(string $customEvaluatorClass, $value, Collection $collection): bool
    {
        $operator = class_basename($customEvaluatorClass);

        $override = $this->customResults[$operator] ?? [];
        /** @var \App\Helpers\Conditions\Evaluators\ShouldEvaluate $customEvaluatorClass */
        $evaluation = $customEvaluatorClass::init($this->building, $this->inputSource, $collection)
            ->override($override)
            ->evaluate($value);

        $this->customResults[$operator][$evaluation['key']] = $evaluation['results'];

        return $evaluation['bool'];
    }

    protected function fetchPassConditions($column, $value, bool $recursive = false): array
    {
        $column = is_array($column) ? $column : ['short' => $column];
        $model = (new $value)->newQuery()->where($column)->first();

        $conditions = $model->conditions ?? [];

        // If we are recursive, we want to continue the same cycle, as the retrieved conditions might also reference
        // other conditions.
        if ($recursive) {
            $passClauses = $this->fetchPassClauses($conditions);

            foreach ($passClauses as $clause) {
                $conditions = array_merge($conditions, $this->fetchPassConditions($clause['column'], $clause['value'], true));
            }
        }

        return $conditions;
    }

    protected function fetchPassClauses(array $conditions): Collection
    {
        return collect(Arr::flatten($conditions, 2))
            ->merge(collect(Arr::flatten($conditions, 1)))
            ->whereIn('operator', [Clause::PASSES, Clause::NOT_PASSES]);
    }
}
