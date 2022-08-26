<?php

namespace App\Helpers\DataTypes;

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

    public function getCast()
    {
        if (is_null($this->value)) {
            return null;
        }

        switch ($this->dataType) {
            case static::STRING:
                $this->value = (string) $this->value;
                break;

            case static::IDENTIFIER:
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
}