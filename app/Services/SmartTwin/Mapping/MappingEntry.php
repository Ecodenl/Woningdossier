<?php

namespace App\Services\SmartTwin\Mapping;

use App\Enums\SmartTwin\MappingStatus;
use App\Enums\SmartTwin\MappingTarget;

/**
 * One leaf plus what the mapping decided about it. One row of the mapping report.
 */
final class MappingEntry
{
    public function __construct(
        public readonly Leaf $leaf,
        public readonly MappingStatus $status,
        public readonly ?string $target = null,
        public readonly mixed $mappedValue = null,
        public readonly ?string $note = null,
        public readonly MappingTarget $kind = MappingTarget::TOOL_QUESTION,
    )
    {
    }

    public static function fromResult(Leaf $leaf, MappingResult $result): self
    {
        return new self($leaf, $result->status, $result->target, $result->value, $result->note, $result->kind);
    }

    /**
     * The value that was written, as it goes into the report.
     *
     * Arrays are rendered as JSON rather than as the `[]` Leaf uses: a checkbox answer is a real
     * list of shorts, and which shorts were saved is the whole point of the row.
     */
    public function displayMappedValue(): string
    {
        return match (true) {
            is_null($this->mappedValue)  => '',
            is_bool($this->mappedValue)  => $this->mappedValue ? 'true' : 'false',
            is_array($this->mappedValue) => json_encode($this->mappedValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            default                      => (string) $this->mappedValue,
        };
    }
}
