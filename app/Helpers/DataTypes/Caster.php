<?php

namespace App\Helpers\DataTypes;

use App\Helpers\NumberFormatter;
use App\Traits\FluentCaller;

class Caster
{
    use FluentCaller;

    // TODO: Convert these into custom classes

    public const string STRING = 'string';
    public const string HTML_STRING = 'html_string'; // String that contains HTML and should be sanitized
    public const string INT = 'int';
    public const string INT_5 = 'int_5'; // INT with bucket 5 for rounding
    public const string FLOAT = 'float';
    public const string NON_ROUNDING_FLOAT = 'non_rounding_float';
    public const string BOOL = 'bool';
    public const string ARRAY = 'array';
    public const string JSON = 'json';
    public const string IDENTIFIER = 'identifier';

    protected string $dataType;
    protected mixed $value;
    protected bool $force = false;

    public function dataType(string $dataType): self
    {
        $this->dataType = $dataType;
        return $this;
    }

    public function value(mixed $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Force defines whether or not converting data should be forced if the value is null.
     */
    public function force(): self
    {
        $this->force = true;
        return $this;
    }

    /**
     * Cast a value to given type.
     */
    public function getCast(): null|int|float|string|bool|array
    {
        if (is_null($this->value) && ! $this->force) {
            return null;
        }

        switch ($this->dataType) {
            case static::STRING:
            case static::HTML_STRING:
                $this->value = (string) $this->value;
                break;

            case static::INT:
                // TODO: If the value is too large, it will bitshift into negative. This might be unexpected behaviour.
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
     */
    public function reverseFormatted(): null|int|float|string
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
     */
    public function getFormatForUser(): null|string
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

            case static::NON_ROUNDING_FLOAT:
            case static::FLOAT:
                $value = NumberFormatter::formatNumberForUser($value, false, false);
                break;
        }

        return $value;
    }
}
