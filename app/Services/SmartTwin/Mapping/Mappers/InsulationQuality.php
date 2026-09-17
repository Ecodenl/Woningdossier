<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

/**
 * Turns an Rc value into the insulation level Hoomdossier offers as an answer.
 *
 * Returns the `calculate_value` of the matching element value: the ordinal the rest of the codebase
 * already keys these options on, and the one the seeder upserts by. The ids differ per environment;
 * these numbers do not.
 *
 * The numbers assume upgrade:add-reasonable-insulation-value has run — "Redelijke isolatie" sits at
 * 4 and pushes "Goede" and "Zeer goede" up to 5 and 6. A mapper that cannot resolve one of them
 * reports it rather than writing something else.
 */
final class InsulationQuality
{
    /**
     * Lower bound of each level for a facade, highest first, per the classification table the
     * advice is based on:
     *
     *   geen        Rc < 0,80
     *   matig       0,80 <= Rc < 2,08
     *   redelijk    2,08 <= Rc < 3,19
     *   goed        3,19 <= Rc < 4,46
     *   zeer goed   Rc >= 4,46
     *
     * Floor and roof run on their own boundaries and get their own table; reusing these would be
     * wrong in a way nothing would catch.
     *
     * @var array<string, int>  Lower bound as a string key, since PHP array keys cannot be floats.
     */
    private const WALL = [
        '4.46' => 6, // Zeer goede isolatie
        '3.19' => 5, // Goede isolatie
        '2.08' => 4, // Redelijke isolatie
        '0.80' => 3, // Matige isolatie
    ];

    /** The level below the lowest bound. */
    private const NONE = 2; // Geen isolatie

    public static function forWall(float $rcValue): int
    {
        foreach (self::WALL as $lowerBound => $calculateValue) {
            if ($rcValue >= (float) $lowerBound) {
                return $calculateValue;
            }
        }

        return self::NONE;
    }
}
