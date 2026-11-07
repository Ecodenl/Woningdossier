<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

/**
 * The ground floors of one section of a SmartTwin response.
 *
 * Nothing beyond the shared sums so far: unlike the facade, where the type decides whether there is
 * a cavity, nothing Hoomdossier asks about the floor hangs off a per-part property. What it does
 * ask about — the crawlspace — sits next to the floors rather than on them, see CrawlSpaces.
 */
final class FloorParts extends InsulatedParts
{
    /**
     * @param  array<string, mixed>  $response
     * @param  string                $section   `current` or `scenario`.
     */
    public static function fromResponse(array $response, string $section): self
    {
        return self::of(self::gather($response, $section, 'floorAssemblies', 'floors'));
    }
}
