<?php

namespace App\Services\SmartTwin\Mapping;

/**
 * One leaf of a SmartTwin response: a value that is not itself a structure.
 *
 * The mapping walks these, so a leaf is the unit a mapper claims and the unit a report row
 * describes. Objects and lists are not leaves — only what hangs at the end of them is.
 */
final class Leaf
{
    public function __construct(
        /** Full path, arrays with their index: `current.properties.roofAssemblies.0.area`. */
        public readonly string $path,
        /** Same path with `*` for every array index: `current.properties.roofAssemblies.*.area`. */
        public readonly string $pathGroup,
        public readonly mixed $value,
    )
    {
    }

    /**
     * The last path segment. Handy to group on when the same field name occurs in several branches.
     */
    public function key(): string
    {
        $segments = explode('.', $this->path);

        return end($segments);
    }

    public function dataType(): string
    {
        return match (true) {
            is_null($this->value)  => 'null',
            is_bool($this->value)  => 'bool',
            is_int($this->value)   => 'int',
            is_float($this->value) => 'float',
            is_array($this->value) => 'array',
            default                => 'string',
        };
    }

    /**
     * The value as it goes into the report. Booleans and null would otherwise turn into `1`/`` and
     * become indistinguishable from a real value; the data type alone cannot repair that afterwards.
     */
    public function displayValue(): string
    {
        return match (true) {
            is_null($this->value)  => '',
            is_bool($this->value)  => $this->value ? 'true' : 'false',
            is_array($this->value) => '[]',
            default                => (string) $this->value,
        };
    }
}
