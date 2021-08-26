<?php

namespace App\Helpers\Conditions;

class Clause
{

    public const EQ = '=';
    public const NEQ = '!=';
    public const GT = '>';
    public const GTE = '>=';
    public const LT = '<';
    public const LTE = '<=';
    public const CONTAINS = 'contains';

    protected string $column;
    protected string $operator;
    protected $value;

    protected $ands = [];

    public function __construct(string $column, string $operator, $value)
    {
        $this->column   = $column;
        $this->operator = $operator;
        $this->value    = $value;
    }

    public function andClause(Clause $clause): self
    {
        $this->ands[] = $clause;

        return $this;
    }

    public function toArray(): array
    {
        $result = [
                [
                    'column'   => $this->column,
                    'operator' => $this->operator,
                    'value'    => $this->value,
                ],
            ];

        foreach ($this->ands as $and) {
            $result = array_merge_recursive($result, $and->toArray());
        }

        return $result;
    }
}