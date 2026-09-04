<?php

namespace App\Services\SmartTwin\Mapping;

use App\Enums\SmartTwin\MappingStatus;

/**
 * One leaf plus what the mapping decided about it. One row of the mapping report.
 */
final class MappingEntry
{
    public function __construct(
        public readonly Leaf $leaf,
        public readonly MappingStatus $status,
        public readonly ?string $target = null,
        public readonly ?string $note = null,
    )
    {
    }

    public static function fromResult(Leaf $leaf, MappingResult $result): self
    {
        return new self($leaf, $result->status, $result->target, $result->note);
    }
}
