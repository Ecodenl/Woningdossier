<?php

namespace App\Helpers\DataTypes;

use App\Helpers\NumberFormatter;
use App\Traits\FluentCaller;

class Caster
{
    use FluentCaller;

    public const STRING = 'string';
    public const INT = 'int';
    public const INT_5 = 'int_5'; // INT with bucket 5 for rounding
    public const FLOAT = 'float';
    public const NON_ROUNDING_FLOAT = 'non_rounding_float';
    public const BOOL = 'bool';
    public const ARRAY = 'array';
    public const JSON = 'json';
    public const IDENTIFIER = 'identifier';

    protected string $dataType;
    protected $value;
    protected bool $force = false;

    public function dataType(string $dataType): self
    {
        $this->dataType = $dataType;
        return $this;
    }

    public function value($value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Force defines whether or not converting data should be forced if the value is null.
     *
     * @return $this
     */
    public function force(): self
    {
        $this->force = true;
        return $this;
    }

    /**
     * Cast a value to given type.
     *
     * @return array|bool|float|int|mixed|string|null
     */
    public function getCast()
    {
        if (is_null($this->value) && ! $this->force) {
            return null;
        }

        switch ($this->dataType) {
            case static::STRING:
                $this->value = (string) $this->value;
                break;

            case static::INT:
                $this->value = (int) round((float) $this->value);
                break;

            case static::INT_5:
                $this->value = (int) NumberFormatter::round((float) $this->value, 5);
                break;

            case static::NON_ROUNDING_FLOAT:
            case static::FLOAT:
                $this->value = (float) $this->value;
                break;

            case static::BOOL:
                $this->value = (bool) $this->value;
                break;

            case static::ARRAY:
                $this->value = (array) $this->value;
                break;

            case static::JSON:
                $this->value = json_decode($this->value, true);
                break;
        }

        return $this->value;
    }

    /**
     * Reverse formatted will mean anything that the code can understand.
     *
     * @return mixed|string|int
     */
    public function reverseFormatted()
    {
        // if needed, the cast can be applied per datatype. (like the forUser method)
        $value = $this->value;
        if (is_null($value) && ! $this->force) {
            return null;
        }

        switch ($this->dataType) {
            case static::INT:
                // Note that the dot is replaced with nothing. We expect a Dutch format.
                $value = (int) NumberFormatter::mathableFormat(str_replace('.', '', ($value ?? 0)), 0);
                break;
            case static::NON_ROUNDING_FLOAT:
                $value = (float) str_replace(',', '.', ($value ?? 0));
                break;
            case static::FLOAT:
                // Note that the dot is replaced with nothing. We expect a Dutch format.
                $value = (float) NumberFormatter::mathableFormat(str_replace('.', '', ($value ?? 0)), 2);
                break;
        }

        return $value;
    }
    /**
     * Format a value to a human format.
     *
     * @return array|bool|float|int|mixed|string|null
     */
    public function getFormatForUser()
    {
        $value = $this->getCast();
        if (is_null($value)) {
            return null;
        }

        switch ($this->dataType) {
            case static::INT:
            case static::INT_5:
                $value = NumberFormatter::formatNumberForUser($value, true, false);
                break;

            case static::FLOAT:
                $value = NumberFormatter::formatNumberForUser($value, false, false);
                break;
        }

        return $value;
    }
}