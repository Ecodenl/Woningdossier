<?php

namespace App\Services\SmartTwin;

/**
 * What SmartTwin told us about one advice, kept on the advice under `extra.smart_twin`.
 *
 * The solution or solutions it came from, and per element of the dwelling the advice works on: the
 * parts it covers, how it is now and how the scenario leaves it. Facts only — SmartTwin's own codes
 * and numbers. Labels, insulation levels and wording are decided where it is shown, so they can
 * change without touching what was stored.
 *
 * Kept on the advice because the raw response does not last, and because this is a snapshot of the
 * scan the price came from: the dwelling may be described differently by the next one.
 *
 * Shape of `toArray()`:
 *
 *     version   int
 *     solutions [{id, name, details: {<id>: {name?, value, unit}}}]
 *     elements  {<element>: {shape?, part_ids, insulated_area?, before: {summary, lines}, after: null|{summary, lines}}}
 *
 * `after` is null when the scenario leaves the element as it is — something SmartTwin's spec rules
 * out for an advised element, but the first example response did it for its floor.
 */
final class SmartTwinAdviceDetails
{
    public const VERSION = 1;

    private const KEY = 'smart_twin';

    /**
     * @param  array<int, array<string, mixed>>     $solutions
     * @param  array<string, array<string, mixed>>  $elements
     */
    public function __construct(
        public readonly array $solutions,
        public readonly array $elements,
        public readonly int $version = self::VERSION,
    )
    {
    }

    /**
     * @param  null|array<string, mixed>  $extra  The advice's `extra` column.
     */
    public static function fromExtra(?array $extra): ?self
    {
        $details = $extra[self::KEY] ?? null;

        if (! is_array($details)) {
            return null;
        }

        return new self(
            $details['solutions'] ?? [],
            $details['elements'] ?? [],
            (int) ($details['version'] ?? self::VERSION),
        );
    }

    /**
     * One solution as stored: its id, name and product details, keyed by the detail's id. A detail
     * keeps SmartTwin's own name for it ("Dikte") since 15 October 2026; details stored before have
     * none, and are shown by their id.
     *
     * @param  array<string, mixed>  $solution  A `scenario.solutions.*` entry of the response.
     * @return array<string, mixed>
     */
    public static function solution(array $solution): array
    {
        $details = [];

        foreach ($solution['details'] ?? [] as $detail) {
            if (isset($detail['id'])) {
                $details[$detail['id']] = [
                    'name'  => $detail['displayName'] ?? null,
                    'value' => $detail['value'] ?? null,
                    'unit'  => $detail['unit'] ?? null,
                ];
            }
        }

        return ['id' => $solution['id'] ?? null, 'name' => $solution['name'] ?? null, 'details' => $details];
    }

    /**
     * The details of two solutions for the same measure, which the action plan holds as one advice.
     * The elements are those of the measure, and so the same for both.
     */
    public function with(self $other): self
    {
        return new self(array_merge($this->solutions, $other->solutions), $this->elements ?: $other->elements);
    }

    /**
     * The value for the advice's `extra` column, keeping whatever else is in there.
     *
     * @param  null|array<string, mixed>  $extra
     * @return array<string, mixed>
     */
    public function intoExtra(?array $extra): array
    {
        return array_merge($extra ?? [], [self::KEY => $this->toArray()]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'version'   => $this->version,
            'solutions' => $this->solutions,
            'elements'  => $this->elements,
        ];
    }
}
