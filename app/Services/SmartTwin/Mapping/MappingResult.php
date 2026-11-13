<?php

namespace App\Services\SmartTwin\Mapping;

use App\Enums\SmartTwin\MappingStatus;
use App\Enums\SmartTwin\MappingTarget;
use App\Services\SmartTwin\SmartTwinAdviceDetails;

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
        public readonly ?SmartTwinAdviceDetails $details = null,
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
     * A mapper that has to empty an answer uses cleared(); one that should leave it alone returns
     * skipped() or valueUnmapped().
     *
     * One write per answer. When several leaves collapse into a single answer — the area of every
     * facade adding up to one surface — exactly one of them carries it and the rest return skipped()
     * pointing at it. The applier writes what it is handed, so two mapped results for the same
     * target means two writes, visible as two rows in the report.
     */
    public static function mapped(string $target, mixed $value, ?string $note = null): self
    {
        return new self(MappingStatus::MAPPED, $target, $value, $note);
    }

    /**
     * An answer that has to be emptied: the response describes nothing for it any more.
     *
     * Not the same as skipped(). An import takes over the whole situation SmartTwin describes, so
     * an answer a previous scan wrote and this one does not support would otherwise stay behind and
     * contradict it. Written as null, which ToolQuestionService turns into a deleted answer or an
     * emptied column depending on where the question saves.
     */
    public static function cleared(string $target, ?string $note = null): self
    {
        return new self(MappingStatus::MAPPED, $target, null, $note);
    }

    /**
     * A roof shape the dwelling no longer has, see MappingTarget::ROOF_TYPE_REMOVAL.
     *
     * @param  string  $roofTypeShort  `flat` or `pitched`.
     */
    public static function roofTypeRemoved(string $roofTypeShort, ?string $note = null): self
    {
        return new self(MappingStatus::MAPPED, $roofTypeShort, null, $note, MappingTarget::ROOF_TYPE_REMOVAL);
    }

    /**
     * The two halves of the response disagree: a solution next to a scenario that leaves its part of
     * the dwelling untouched, or the other way round. SmartTwin's spec rules it out, so it is
     * reported for Twee Snoeken — and nothing is written: answers follow the properties, advices the
     * solutions, and neither is corrected by the other.
     */
    public static function specViolation(string $note): self
    {
        return new self(MappingStatus::SPEC_VIOLATION, null, null, $note);
    }

    /**
     * A measure SmartTwin advises, with what it costs.
     *
     * Not a tool question: this becomes an entry in the action plan that the calculation does not
     * own and will not rebuild. See App\Enums\AdviceSource.
     *
     * @param  string                        $measureShort  The short of the measure application being advised.
     * @param  float                         $costs         What the measure costs, already totalled.
     * @param  null|SmartTwinAdviceDetails   $details       What the advice carries along for the woonplan.
     */
    public static function mappedAdvice(
        string $measureShort,
        float $costs,
        ?string $note = null,
        ?SmartTwinAdviceDetails $details = null,
    ): self
    {
        return new self(MappingStatus::MAPPED, $measureShort, $costs, $note, MappingTarget::ADVICE, $details);
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
