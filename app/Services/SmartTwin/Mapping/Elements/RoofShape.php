<?php

namespace App\Services\SmartTwin\Mapping\Elements;

/**
 * Flat or pitched, the split Hoomdossier asks every roof question along.
 *
 * SmartTwin does not give it. Its `roofType` is the construction — wooden beam layer, concrete
 * floor, roof sheet — which says nothing about the slope, so the inclination of the roof assembly
 * is the only thing to go by.
 */
enum RoofShape: string
{
    case FLAT = 'flat';
    case PITCHED = 'pitched';

    /** Stakeholder decision: up to and including 10° a roof is flat. */
    public const FLAT_UP_TO_DEGREES = 10;

    public static function forInclination(?int $inclination): self
    {
        return ! is_null($inclination) && $inclination > self::FLAT_UP_TO_DEGREES ? self::PITCHED : self::FLAT;
    }

    /** The short of the RoofType Hoomdossier keeps a building_roof_types row per. */
    public function roofTypeShort(): string
    {
        return $this->value;
    }
}
