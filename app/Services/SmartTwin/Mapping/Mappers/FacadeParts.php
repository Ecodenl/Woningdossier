<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

/**
 * The closed facade parts of one section of a SmartTwin response.
 *
 * SmartTwin describes a dwelling as assemblies, one per orientation, each holding its closed facade
 * parts next to its windows, doors and panels. Hoomdossier knows a single facade, so everything
 * here collapses many parts into one number.
 *
 * Named FacadeParts rather than Facades to stay out of the way of Laravel's facades.
 */
final class FacadeParts
{
    /**
     * Rc values come out of a JSON document as floats. An untouched part carries the exact same
     * value in both sections, so this only has to absorb representation noise, not real change.
     */
    private const IMPROVEMENT_TOLERANCE = 0.001;

    /**
     * @param  array<int, array<string, mixed>>  $parts
     */
    private function __construct(private readonly array $parts)
    {
    }

    /**
     * @param  array<string, mixed>  $response
     * @param  string                $section   `current` or `scenario`.
     */
    public static function fromResponse(array $response, string $section): self
    {
        $parts = [];

        foreach ($response[$section]['properties']['facadeAssemblies'] ?? [] as $assembly) {
            foreach ($assembly['facades'] ?? [] as $part) {
                $parts[] = $part;
            }
        }

        return new self($parts);
    }

    public function count(): int
    {
        return count($this->parts);
    }

    /**
     * The closed facade surface: the parts themselves, without the windows, doors and panels that
     * make up the rest of an assembly. Per assembly SmartTwin's own `area` is exactly the four
     * added together, so leaving the openings out is what separates this from the gross surface.
     */
    public function totalArea(): float
    {
        return array_sum(array_map(fn (array $part) => (float) ($part['area'] ?? 0), $this->parts));
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

    /**
     * The facade type covering the most surface, or null when no part states one.
     *
     * A dwelling can hold parts of several types while Hoomdossier asks a single yes or no about a
     * cavity wall, so the largest surface decides. A tie goes to the lowest type number, which keeps
     * the answer the same from one run to the next.
     */
    public function dominantType(): ?int
    {
        $areaPerType = [];

        foreach ($this->parts as $part) {
            if (! isset($part['facadeType'])) {
                continue;
            }

            $type = (int) $part['facadeType'];
            $areaPerType[$type] = ($areaPerType[$type] ?? 0) + (float) ($part['area'] ?? 0);
        }

        if (empty($areaPerType)) {
            return null;
        }

        ksort($areaPerType);

        return (int) array_search(max($areaPerType), $areaPerType, true);
    }
}
