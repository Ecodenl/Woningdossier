<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

/**
 * Turns SmartTwin's crawlspace height into the option Hoomdossier offers.
 *
 * SmartTwin reports `heightAboveGroundLevel` in metres, relative to ground level and negative when
 * the crawlspace floor lies below it — which is the usual case. Hoomdossier only asks how much room
 * there is, so the sign carries no meaning here and the absolute value decides.
 *
 * Returns the element value's `order`, not its calculate value, which is unusual and deliberate:
 * "Heel laag (minder dan 30 cm)" and "Onbekend" both carry calculate value 0, so that is the one
 * thing that cannot tell them apart. Nothing else in the codebase needs to — it only ever looks up
 * 45 and 30 — but this does.
 */
final class CrawlspaceHeight
{
    /** The `order` of each option, as seeded on the crawlspace element. */
    public const HIGH = 0;    // Best hoog (meer dan 45 cm)
    public const LOW = 1;     // Laag (tussen 30 en 45 cm)
    public const VERY_LOW = 2; // Heel laag (minder dan 30 cm)
    public const UNKNOWN = 3;  // Onbekend

    /**
     * Where the bands meet, in metres. A height on a boundary belongs to the band above it:
     * `0,5 <= h` is the highest, `0,3 <= h < 0,5` the middle, and anything under 0,3 the lowest.
     * That matters — 0,5 is exactly what the sample dossier carries.
     */
    private const HIGH_FROM = 0.5;
    private const LOW_FROM = 0.3;

    public static function orderFor(?float $heightAboveGroundLevel): int
    {
        if (is_null($heightAboveGroundLevel)) {
            return self::UNKNOWN;
        }

        $height = abs($heightAboveGroundLevel);

        return match (true) {
            $height >= self::HIGH_FROM  => self::HIGH,
            $height >= self::LOW_FROM   => self::LOW,
            default                     => self::VERY_LOW,
        };
    }

    /**
     * The height and the band it falls in, for the mapping report: "hoogte 0,50 m t.o.v. maaiveld
     * valt vanaf 0,50 m". Quotes the boundaries of the table rather than an option's order or id.
     */
    public static function describe(?float $heightAboveGroundLevel): string
    {
        if (is_null($heightAboveGroundLevel)) {
            return 'geen hoogte opgegeven';
        }

        $height = abs($heightAboveGroundLevel);
        $metres = fn (float $value) => InsulationQuality::decimal($value) . ' m';

        $band = match (true) {
            $height >= self::HIGH_FROM => 'vanaf ' . $metres(self::HIGH_FROM),
            $height >= self::LOW_FROM  => 'tussen ' . $metres(self::LOW_FROM) . ' en ' . $metres(self::HIGH_FROM),
            default                    => 'onder ' . $metres(self::LOW_FROM),
        };

        return 'hoogte ' . $metres($height) . " t.o.v. maaiveld valt {$band}";
    }
}
