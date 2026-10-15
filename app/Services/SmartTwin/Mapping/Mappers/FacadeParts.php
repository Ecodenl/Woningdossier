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
}
