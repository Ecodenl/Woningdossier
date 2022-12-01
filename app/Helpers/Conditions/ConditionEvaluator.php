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

    /**
     * @var Building
     */
    protected $building;
    /**
     * @var InputSource
     */
    protected $inputSource;

    protected bool $explain = false;

    /**
     * @param  Building  $building
     *
     * @return $this
     */
    public function building(Building $building)
    {
        $this->building = $building;

        return $this;
    }

    /**
     * @param  InputSource  $inputSource
     *
     * @return $this
     */
    public function inputSource(InputSource $inputSource)
    {
        $this->inputSource = $inputSource;

        return $this;
    }

    public function explain()
    {
        $this->explain = true;

        return $this;
    }

    public function getToolAnswersForConditions(array $conditions): Collection
    {
        // Get answers for condition columns, but ensure we don't fetch PASS clause columns (as they don't have
        // any answers so they don't need checking, and they could be arrays which would cause issues), and also
        // don't fetch special evaluators, as they don't have answers either.
        $questionKeys = collect(Arr::flatten($conditions, 2))
            ->merge(collect(Arr::flatten($conditions, 1)))
            ->whereNotIn('operator', [Clause::PASSES, Clause::NOT_PASSES])
            ->where('column', '!=', 'fn')
            ->pluck('column')
            ->unique()
            ->filter()
            ->values();

        // the structure of the questionKeys tells us how to retrieve the answer
        // if it contains a dot, it's in a table.column format
        // if not, it's a tool question short
        $answers = [];
        foreach ($questionKeys as $questionKey) {
            if (Str::contains($questionKey, '.',)) {
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
                if (is_array($answer)) {
                    $answer = collect($answer);
                }
            }
            $answers[$questionKey] = $answer;
        }

        return collect($answers);
    }

    public function evaluate(array $conditions): bool
    {
        $answers = $this->getToolAnswersForConditions($conditions);

        return $this->evaluateCollection($conditions, $answers);
    }

    public function evaluateCollection(array $conditions, Collection $collection)
    {
        $result = false;
        foreach ($conditions as $andClause) {
            $result = $result || $this->evaluateAnd($andClause, $collection);
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
            return $customEvaluatorClass::evaluate($this->building, $this->inputSource, $value ?? null, $collection);
        }

        // Else check if we should do sub-evaluation
        if ($operator === Clause::PASSES || $operator === Clause::NOT_PASSES) {
            $column = is_array($column) ? $column : ['short' => $column];
            $model = (new $value)->newQuery()->where($column)->first();

            // Ensure we pass potential dynamic answers through
            $conditions = $model->conditions ?? [];
            $answersForNewConditions = $this->getToolAnswersForConditions($conditions)->merge($collection);

            // Return result based on whether it should or should not pass
            $result = $this->evaluateCollection($conditions, $answersForNewConditions);
            return $operator === Clause::PASSES ? $result : ! $result;
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

}