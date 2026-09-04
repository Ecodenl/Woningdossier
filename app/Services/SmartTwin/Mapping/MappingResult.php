<?php

namespace App\Services\SmartTwin\Mapping;

use App\Enums\SmartTwin\MappingStatus;

/**
 * What a mapper decided about one leaf.
 *
 * A result says what should happen, not what happened: mappers describe, the walk applies. That
 * keeps a dry run possible — map, report, write nothing — which is what you want while tuning the
 * mapping, and it keeps mappers testable without a database.
 */
final class MappingResult
{
    private function __construct(
        public readonly MappingStatus $status,
        public readonly ?string $target = null,
        public readonly ?string $note = null,
    )
    {
    }

    /**
     * @param  string  $target  Where the value goes, e.g. a tool question short.
     */
    public static function mapped(string $target, ?string $note = null): self
    {
        return new self(MappingStatus::MAPPED, $target, $note);
    }

    /**
     * The field is mapped, but this value has no counterpart on our side — an unknown heating type,
     * an unknown label. The one to watch: it fails silently in production and looks like a completed
     * scan with a missing answer.
     */
    public static function valueUnmapped(?string $note = null): self
    {
        return new self(MappingStatus::VALUE_UNMAPPED, null, $note);
    }

    /**
     * The mapping points at something that does not (or no longer) exist. A bug in the mapping
     * rather than a gap in it.
     */
    public static function targetMissing(string $target, ?string $note = null): self
    {
        return new self(MappingStatus::TARGET_MISSING, $target, $note);
    }

    /**
     * Deliberately ignored: ids, metadata, or an echo of what we sent SmartTwin ourselves.
     */
    public static function skipped(?string $note = null): self
    {
        return new self(MappingStatus::SKIPPED, null, $note);
    }
}
