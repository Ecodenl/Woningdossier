<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

/**
 * Turns an Rc value into the insulation level Hoomdossier offers as an answer.
 *
 * Returns the `calculate_value` of the matching element value: the ordinal the rest of the codebase
 * already keys these options on, and the one the seeder upserts by. The ids differ per environment;
 * these numbers do not.
 *
 * The numbers assume upgrade:extend-insulation-scales has run. A mapper that cannot resolve one of
 * them reports it rather than writing something else.
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

    /**
     * The same for a ground floor, whose boundaries are its own and whose scale has one level more:
     *
     *   geen        Rc < 0,20
     *   slecht      0,20 <= Rc < 1,00
     *   matig       1,00 <= Rc < 1,75
     *   redelijk    1,75 <= Rc < 3,00
     *   goed        3,00 <= Rc < 4,35
     *   zeer goed   Rc >= 4,35
     *
     * That extra level is why the floor's numbers run one higher than the wall's from Matige
     * isolatie up; see ElementValue::insulatedFromCalculateValue(), which draws the line between
     * needing insulation and having it at a different place for the same reason.
     *
     * @var array<string, int>
     */
    private const FLOOR = [
        '4.35' => 7, // Zeer goede isolatie
        '3.00' => 6, // Goede isolatie
        '1.75' => 5, // Redelijke isolatie
        '1.00' => 4, // Matige isolatie
        '0.20' => 3, // Slechte isolatie
    ];

    /** The level below the lowest bound, per element. */
    private const NONE = 2; // Geen isolatie

    public static function forWall(float $rcValue): int
    {
        return self::classify($rcValue, self::WALL);
    }

    public static function forFloor(float $rcValue): int
    {
        return self::classify($rcValue, self::FLOOR);
    }

    /**
     * @param  array<string, int>  $bounds  Lower bound => calculate value, highest first.
     */
    private static function classify(float $rcValue, array $bounds): int
    {
        foreach ($bounds as $lowerBound => $calculateValue) {
            if ($rcValue >= (float) $lowerBound) {
                return $calculateValue;
            }
        }

        return self::NONE;
    }
}
