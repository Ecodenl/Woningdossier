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
     * The parts of this set whose Rc goes up compared to the set it came from: the parts that get
     * insulated.
     *
     * Matched on SmartTwin's `id`, which a part keeps between the two sections. Matching on position
     * held for every response seen so far, but nothing promises it, and a wrong pairing silently
     * produces a surface. A part without an id, or one the other section does not hold, cannot be
     * compared and is not counted.
     */
    public function improvedOver(self $before): static
    {
        $was = [];

        foreach ($before->parts as $part) {
            if (isset($part['id'])) {
                $was[$part['id']] = (float) ($part['rcValue'] ?? 0);
            }
        }

        return $this->filter(function (array $part) use ($was) {
            $id = $part['id'] ?? null;

            return ! is_null($id)
                && array_key_exists($id, $was)
                && (float) ($part['rcValue'] ?? 0) > $was[$id] + self::IMPROVEMENT_TOLERANCE;
        });
    }

    /**
     * The surface the scenario insulates, holding this set against the one it came from.
     */
    public function areaImprovedOver(self $before): float
    {
        return $this->improvedOver($before)->totalArea();
    }

    public function filter(callable $keep): static
    {
        return static::of(array_values(array_filter($this->parts, $keep)));
    }

    /**
     * @return array<int, string>
     */
    public function ids(): array
    {
        return array_values(array_filter(array_map(fn (array $part) => $part['id'] ?? null, $this->parts)));
    }

    /**
     * The type covering the most surface, or null when no part states one.
     *
     * A dwelling can hold parts of several types while Hoomdossier asks about one, so the largest
     * surface decides. A tie goes to the lowest type number, which keeps the answer the same from
     * one run to the next.
     */
    public function dominantType(string $typeField): ?int
    {
        $areaPerType = [];

        foreach ($this->parts as $part) {
            if (! isset($part[$typeField])) {
                continue;
            }

            $type = (int) $part[$typeField];
            $areaPerType[$type] = ($areaPerType[$type] ?? 0) + (float) ($part['area'] ?? 0);
        }

        if (empty($areaPerType)) {
            return null;
        }

        ksort($areaPerType);

        return (int) array_search(max($areaPerType), $areaPerType, true);
    }

    /**
     * The parts grouped by type and Rc value, largest surface first: one line per kind of part, as
     * the woonplan lists them. The Rc is grouped on two decimals, the precision of every table it
     * is held against, so float noise does not split a line.
     *
     * @return array<int, array{type: mixed, rc_value: float, area: float}>
     */
    public function lines(string $typeField): array
    {
        $lines = [];

        foreach ($this->parts as $part) {
            $type = $part[$typeField] ?? null;
            $rcValue = round((float) ($part['rcValue'] ?? 0), 2);
            $key = ($type ?? 'null') . '|' . $rcValue;

            $lines[$key] ??= ['type' => $type, 'rc_value' => $rcValue, 'area' => 0.0];
            $lines[$key]['area'] += (float) ($part['area'] ?? 0);
        }

        $lines = array_map(fn (array $line) => array_merge($line, ['area' => round($line['area'], 2)]), array_values($lines));
        usort($lines, fn (array $a, array $b) => $b['area'] <=> $a['area']);

        return $lines;
    }
}
