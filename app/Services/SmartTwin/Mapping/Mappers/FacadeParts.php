<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

/**
 * The closed facade parts of one section of a SmartTwin response.
 *
 * Named FacadeParts rather than Facades to stay out of the way of Laravel's facades.
 */
final class FacadeParts extends InsulatedParts
{
    /**
     * @param  array<string, mixed>  $response
     * @param  string                $section   `current` or `scenario`.
     */
    public static function fromResponse(array $response, string $section): self
    {
        return self::of(self::gather($response, $section, 'facadeAssemblies', 'facades'));
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
