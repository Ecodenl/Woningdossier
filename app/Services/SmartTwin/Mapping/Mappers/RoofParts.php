<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Services\SmartTwin\Mapping\Elements\RoofShape;

/**
 * The roofs of one section of a SmartTwin response, each knowing the slope of its assembly.
 *
 * The slope is what tells a flat roof from a pitched one (see RoofShape); it sits on the assembly,
 * not on the roof part, so it is copied onto every part on the way in, along with the shape it
 * makes.
 */
final class RoofParts extends InsulatedParts
{
    /**
     * @param  array<string, mixed>  $response
     * @param  string                $section   `current` or `scenario`.
     */
    public static function fromResponse(array $response, string $section): self
    {
        $parts = [];

        foreach ($response[$section]['properties']['roofAssemblies'] ?? [] as $assembly) {
            foreach ($assembly['roofs'] ?? [] as $roof) {
                $roof['inclination'] = $assembly['inclination'] ?? null;
                $roof['shape'] = RoofShape::forInclination(isset($roof['inclination']) ? (int) $roof['inclination'] : null)->value;
                $parts[] = $roof;
            }
        }

        return self::of($parts);
    }

    public function ofShape(RoofShape $shape): self
    {
        return $this->filter(fn (array $part) => $shape->value === ($part['shape'] ?? null));
    }

    /**
     * The shape covering the most surface, or null without a roof. A tie goes to pitched, the shape
     * Hoomdossier lists first.
     */
    public function dominantShape(): ?RoofShape
    {
        $pitched = $this->ofShape(RoofShape::PITCHED)->totalArea();
        $flat = $this->ofShape(RoofShape::FLAT)->totalArea();

        return match (true) {
            0 === $this->count() => null,
            $flat > $pitched     => RoofShape::FLAT,
            default              => RoofShape::PITCHED,
        };
    }
}
