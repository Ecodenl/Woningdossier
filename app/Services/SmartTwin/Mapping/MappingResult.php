<?php

namespace App\Services\SmartTwin\Mapping;

use App\Enums\SmartTwin\MappingStatus;
use App\Enums\SmartTwin\MappingTarget;

/**
 * What a mapper decided about one leaf.
 *
 * A result says what should happen, not what happened: mappers describe, MappingApplier writes.
 * That keeps mappers testable without a database, and keeps every write going through one place.
 */
final class MappingResult
{
    private function __construct(
        public readonly MappingStatus $status,
        public readonly ?string $target = null,
        public readonly mixed $value = null,
        public readonly ?string $note = null,
        public readonly MappingTarget $kind = MappingTarget::TOOL_QUESTION,
    )
    {
    }

    /**
     * @param  string  $target  The tool question short the value is saved under.
     * @param  mixed   $value   The value as the tool question expects it: a model id for an
     *                          IDENTIFIER question, a float for a FLOAT one, an array of shorts for
     *                          a checkbox. Translating the SmartTwin value into that shape is the
     *                          mapper's job; nothing downstream casts.
     *
     * A mapper with nothing to save returns skipped() or valueUnmapped() rather than a null value.
     *
     * One leaf, one write. When several leaves of a path group collapse into a single answer — the
     * area of every facade adding up to one surface — exactly one of them returns mapped() and the
     * rest return skipped() pointing at it. The applier writes what it is handed, so two mapped
     * results for the same target means two writes, visible as two rows in the report.
     */
    public static function mapped(string $target, mixed $value, ?string $note = null): self
    {
        return new self(MappingStatus::MAPPED, $target, $value, $note);
    }

    /**
     * A measure SmartTwin advises, with what it costs.
     *
     * Not a tool question: this becomes an entry in the action plan that the calculation does not
     * own and will not rebuild. See App\Enums\AdviceSource.
     *
     * @param  string  $measureShort  The short of the measure application being advised.
     * @param  float   $costs         What the measure costs, already totalled.
     */
    public static function mappedAdvice(string $measureShort, float $costs, ?string $note = null): self
    {
        return new self(MappingStatus::MAPPED, $measureShort, $costs, $note, MappingTarget::ADVICE);
    }

    /**
     * The field is mapped, but this value has no counterpart on our side — an unknown heating type,
     * an unknown label. The one to watch: it fails silently in production and looks like a completed
     * scan with a missing answer.
     */
    public static function valueUnmapped(?string $note = null): self
    {
        return new self(MappingStatus::VALUE_UNMAPPED, null, null, $note);
    }

    /**
     * The mapping points at something that does not (or no longer) exist. A bug in the mapping
     * rather than a gap in it.
     *
     * Keeps the value it was going to write, so the report shows what was lost and not just where.
     */
    public static function targetMissing(
        string $target,
        mixed $value = null,
        ?string $note = null,
        MappingTarget $kind = MappingTarget::TOOL_QUESTION,
    ): self
    {
        return new self(MappingStatus::TARGET_MISSING, $target, $value, $note, $kind);
    }

    /**
     * Deliberately ignored: ids, metadata, an echo of what we sent SmartTwin ourselves, or a
     * sibling leaf that another leaf of the same path group already accounts for.
     */
    public static function skipped(?string $note = null): self
    {
        return new self(MappingStatus::SKIPPED, null, null, $note);
    }
}
