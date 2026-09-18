<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

/**
 * The insulated parts of one building element, in one section of a SmartTwin response.
 *
 * SmartTwin describes a dwelling as assemblies holding parts — facades in facade assemblies, floors
 * in floor assemblies — each with its own surface and Rc value. Hoomdossier asks one question per
 * element, so everything here collapses many parts into one number.
 *
 * The three sums are the same whichever element it is, which is why they live here rather than once
 * per mapper. What differs per element is what else a part carries, and that stays in the subclass.
 */
abstract class InsulatedParts
{
    /**
     * Rc values come out of a JSON document as floats. An untouched part carries the exact same
     * value in both sections, so this only has to absorb representation noise, not real change.
     */
    private const IMPROVEMENT_TOLERANCE = 0.001;

    /**
     * @param  array<int, array<string, mixed>>  $parts
     */
    final protected function __construct(protected readonly array $parts)
    {
    }

    /**
     * @param  array<int, array<string, mixed>>  $parts
     */
    final protected static function of(array $parts): static
    {
        return new static($parts);
    }

    /**
     * Every part of one kind, out of the assemblies that hold them.
     *
     * @param  array<string, mixed>  $response
     * @param  string                $section     `current` or `scenario`.
     * @param  string                $assemblies  e.g. `facadeAssemblies`.
     * @param  string                $key         e.g. `facades`.
     * @return array<int, array<string, mixed>>
     */
    final protected static function gather(array $response, string $section, string $assemblies, string $key): array
    {
        $parts = [];

        foreach ($response[$section]['properties'][$assemblies] ?? [] as $assembly) {
            foreach ($assembly[$key] ?? [] as $part) {
                $parts[] = $part;
            }
        }

        return $parts;
    }

    public function count(): int
    {
        return count($this->parts);
    }

    /**
     * The surface of the parts themselves, without the openings that make up the rest of an
     * assembly. Per facade assembly SmartTwin's own `area` is the facades, windows, doors and panels
     * added together, so leaving those out is what separates this from the gross surface.
     */
    public function totalArea(): float
    {
        return array_sum(array_map(fn (array $part) => (float) ($part['area'] ?? 0), $this->parts));
    }

    /**
     * The Rc value of the element as a whole, weighted by surface, or null when there is no surface
     * to weigh with.
     *
     * A dwelling can hold a well insulated extension next to an untouched original, and it is the
     * combination Hoomdossier asks about. Weighting by area rather than averaging flat keeps a two
     * square metre porch from counting as much as a whole side of the house.
     */
    public function weightedRcValue(): ?float
    {
        $area = $this->totalArea();

        if ($area <= 0) {
            return null;
        }

        $weighted = array_sum(array_map(
            fn (array $part) => (float) ($part['rcValue'] ?? 0) * (float) ($part['area'] ?? 0),
            $this->parts,
        ));

        return $weighted / $area;
    }

    /**
     * The surface the advice insulates, found by holding this set against the one it came from: a
     * part whose Rc goes up is a part that gets treated.
     *
     * Parts are matched by position. Both sections describe the same dwelling and SmartTwin returns
     * them in the same order, which holds for every response seen so far. Null when the two sections
     * disagree on how many parts there are — then the pairing is a guess, and a guess here silently
     * produces a surface that costs and savings are calculated over.
     */
    public function areaImprovedOver(self $before): ?float
    {
        if ($this->count() !== $before->count()) {
            return null;
        }

        $area = 0.0;

        foreach ($this->parts as $index => $part) {
            $was = (float) ($before->parts[$index]['rcValue'] ?? 0);
            $becomes = (float) ($part['rcValue'] ?? 0);

            if ($becomes > $was + self::IMPROVEMENT_TOLERANCE) {
                $area += (float) ($part['area'] ?? 0);
            }
        }

        return $area;
    }
}
