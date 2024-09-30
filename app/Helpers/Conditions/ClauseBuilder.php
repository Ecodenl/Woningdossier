<?php

namespace App\Helpers\Conditions;

class ClauseBuilder
{

    protected array $clauses = [];

    /**
     * Init optionally with clauses already present.
     */
    public function __construct(array $clauses = [])
    {
        $this->clauses = $clauses;
    }

    /**
     * Adds an "or" to the builder.
     */
    public function orClause(Clause $clause): static
    {
        $this->clauses [] = $clause;

        return $this;
    }

    /**
     * Init from array. This is for conditions coming from the database.
     */
    public static function fromArray(array $conditions): ClauseBuilder
    {
        $clauses = [];
        foreach ($conditions as $andClauses) {
            $and = [];
            foreach ($andClauses as $clause) {
                $c     = new Clause(
                    $clause['column'],
                    $clause['operator'],
                    $clause['value']
                );
                $and[] = $c;
            }
            $clauses [] = $and;
        }

        return new self($clauses);
    }

    /**
     * Output the full logic in array form.
     */
    public function toArray(): array
    {
        $result = [];
        foreach ($this->clauses as $andClauses) {
            $ands = [];
            /** @var Clause $andClause */
            foreach ($andClauses as $andClause) {
                $ands = array_merge_recursive($ands, $andClause->toArray());
            }
            $result[] = $ands;
        }

        return $result;
    }

}