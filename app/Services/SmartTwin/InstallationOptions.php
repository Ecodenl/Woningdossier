<?php

namespace App\Services\SmartTwin;

/**
 * The heat pump types and panel orientations SmartTwin can describe and Hoomdossier had no option
 * for, next to the ones it had (decision 2026-11-07-smarttwin-installaties-mapping, V1 and P-nieuw).
 *
 * One list for everyone who needs them: the seeders for a fresh install, the upgrade command for an
 * existing one, and the mappers that translate SmartTwin's codes. The added options are hidden from
 * the scans (ToolQuestionsTableSeeder's shown_in_scans) and only come from SmartTwin; reading an
 * answer back finds them.
 */
final class InstallationOptions
{
    /** SmartTwin's heatPumpSource. */
    public const SOURCE_UNKNOWN = 0;
    public const SOURCE_GROUND = 1;
    public const SOURCE_GROUNDWATER = 2;
    public const SOURCE_SURFACE_WATER = 3;
    public const SOURCE_OUTSIDE_AIR = 4;
    public const SOURCE_VENTILATION_AIR = 5;
    public const SOURCE_HT = 6;

    /**
     * Every heat pump type SmartTwin can describe, by the order of the heat pump service value: whether
     * it is hybrid, its source, the short of the matching new-heat-pump-type answer and, for the ones
     * added for SmartTwin, the label. Orders 1 to 6 are the original options; 3 and 6 (pvt panels) are
     * not in here, as SmartTwin has no such source.
     *
     * @var array<int, array{hybrid: bool, source: int, new: string, label?: string}>
     */
    public const HEAT_PUMP_TYPES = [
        1  => ['hybrid' => true, 'source' => self::SOURCE_OUTSIDE_AIR, 'new' => 'hybrid-heat-pump-outside-air'],
        2  => ['hybrid' => true, 'source' => self::SOURCE_VENTILATION_AIR, 'new' => 'hybrid-heat-pump-ventilation-air'],
        4  => ['hybrid' => false, 'source' => self::SOURCE_OUTSIDE_AIR, 'new' => 'full-heat-pump-outside-air'],
        5  => ['hybrid' => false, 'source' => self::SOURCE_GROUND, 'new' => 'full-heat-pump-ground-heat'],
        7  => ['hybrid' => true, 'source' => self::SOURCE_GROUND, 'new' => 'hybrid-heat-pump-ground-heat', 'label' => 'Hybride warmtepomp met bodemwarmte'],
        8  => ['hybrid' => true, 'source' => self::SOURCE_GROUNDWATER, 'new' => 'hybrid-heat-pump-groundwater', 'label' => 'Hybride warmtepomp met grondwater'],
        9  => ['hybrid' => true, 'source' => self::SOURCE_SURFACE_WATER, 'new' => 'hybrid-heat-pump-surface-water', 'label' => 'Hybride warmtepomp met oppervlaktewater'],
        10 => ['hybrid' => true, 'source' => self::SOURCE_HT, 'new' => 'hybrid-heat-pump-ht-source', 'label' => 'Hybride warmtepomp met HT-bron'],
        11 => ['hybrid' => true, 'source' => self::SOURCE_UNKNOWN, 'new' => 'hybrid-heat-pump-unknown-source', 'label' => 'Hybride warmtepomp, bron onbekend'],
        12 => ['hybrid' => false, 'source' => self::SOURCE_VENTILATION_AIR, 'new' => 'full-heat-pump-ventilation-air', 'label' => 'Volledige warmtepomp met ventilatielucht'],
        13 => ['hybrid' => false, 'source' => self::SOURCE_GROUNDWATER, 'new' => 'full-heat-pump-groundwater', 'label' => 'Volledige warmtepomp met grondwater'],
        14 => ['hybrid' => false, 'source' => self::SOURCE_SURFACE_WATER, 'new' => 'full-heat-pump-surface-water', 'label' => 'Volledige warmtepomp met oppervlaktewater'],
        15 => ['hybrid' => false, 'source' => self::SOURCE_HT, 'new' => 'full-heat-pump-ht-source', 'label' => 'Volledige warmtepomp met HT-bron'],
        16 => ['hybrid' => false, 'source' => self::SOURCE_UNKNOWN, 'new' => 'full-heat-pump-unknown-source', 'label' => 'Volledige warmtepomp, bron onbekend'],
    ];

    /** The last of the original heat pump types; the ones after it are SmartTwin's. */
    public const LAST_ORIGINAL_HEAT_PUMP_ORDER = 6;

    /**
     * SmartTwin's orientation of panels and collectors => the short of the panel orientation. North
     * and horizontal were added for SmartTwin (with their label and order); the others existed.
     *
     * @var array<int, array{short: string, label?: string, order?: int}>
     */
    public const ORIENTATIONS = [
        0 => ['short' => 'horizontal', 'label' => 'horizontaal', 'order' => 13],
        1 => ['short' => 'n', 'label' => 'noord', 'order' => 14],
        2 => ['short' => 'n-o'],
        3 => ['short' => 'o'],
        4 => ['short' => 'z-o'],
        5 => ['short' => 'z'],
        6 => ['short' => 'z-w'],
        7 => ['short' => 'w'],
        8 => ['short' => 'n-w'],
    ];

    /** The heat pump service value order for a hybrid or full heat pump with this source. */
    public static function heatPumpOrder(bool $hybrid, int $source): ?int
    {
        foreach (self::HEAT_PUMP_TYPES as $order => $type) {
            if ($type['hybrid'] === $hybrid && $type['source'] === $source) {
                return $order;
            }
        }

        return null;
    }

    /**
     * The heat pump types added for SmartTwin: order => label and comfort, as the originals have.
     *
     * @return array<int, array{label: string, comfort: int}>
     */
    public static function addedHeatPumpTypes(): array
    {
        $added = [];

        foreach (self::HEAT_PUMP_TYPES as $order => $type) {
            if (isset($type['label'])) {
                $added[$order] = ['label' => $type['label'], 'comfort' => $type['hybrid'] ? 2 : 3];
            }
        }

        return $added;
    }

    /**
     * The panel orientations added for SmartTwin: short => label and order.
     *
     * @return array<string, array{label: string, order: int}>
     */
    public static function addedOrientations(): array
    {
        $added = [];

        foreach (self::ORIENTATIONS as $orientation) {
            if (isset($orientation['label'])) {
                $added[$orientation['short']] = ['label' => $orientation['label'], 'order' => $orientation['order']];
            }
        }

        return $added;
    }

    /** Whether the scans offer this panel orientation; the added ones only come from SmartTwin. */
    public static function isOrientationAskedInScans(string $short): bool
    {
        return ! array_key_exists($short, self::addedOrientations());
    }
}
