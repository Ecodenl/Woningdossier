<?php

namespace App\Helpers\DataTypes;

use App\Helpers\NumberFormatter;
use App\Traits\FluentCaller;

class Caster
{
    use FluentCaller;

    public const STRING = 'string';
    public const INT = 'int';
    public const FLOAT = 'float';
    public const BOOL = 'bool';
    public const ARRAY = 'array';
    public const JSON = 'json';
    public const IDENTIFIER = 'identifier';

    protected string $dataType;
    protected $value;

    public function __construct(string $dataType, $value)
    {
        $this->dataType = $dataType;
        $this->value = $value;
    }

    /**
     * Cast a value to given type.
     *
     * @return array|bool|float|int|mixed|string|null
     */
    public function getCast()
    {
        if (is_null($this->value)) {
            return null;
        }

        switch ($this->dataType) {
            case static::STRING:
                $this->value = (string) $this->value;
                break;

            case static::INT:
                $this->value = (int) $this->value;
                break;

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
                $value = NumberFormatter::formatNumberForUser($value, true, false);
                break;

            case static::FLOAT:
                $value = NumberFormatter::formatNumberForUser($value, false, false);
                break;
        }

        return $value;
    }
}