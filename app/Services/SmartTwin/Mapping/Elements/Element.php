<?php

namespace App\Services\SmartTwin\Mapping\Elements;

/**
 * The parts of a dwelling SmartTwin describes, as Hoomdossier groups them.
 *
 * One list for everything that reads a dwelling out of a SmartTwin response: the answers, the
 * details on an advice, and later the explanation page. Adding an element is a case here and the
 * reading that goes with it; nothing is organised per measure, of which there are far more.
 */
enum Element: string
{
    case FACADE = 'facade';
    case ROOF = 'roof';
    case FLOOR = 'floor';
    case CRAWLSPACE = 'crawlspace';
    case WINDOWS = 'windows';
    case DOORS = 'doors';
    case PANELS = 'panels';
    case PV = 'pv';
    case VENTILATION = 'ventilation';
    case HEATING = 'heating';
    case HOT_WATER = 'hot-water';
    case COOLING = 'cooling';
    case SOLAR_THERMAL = 'solar-thermal';

    /** Bouwkundig or installaties, the two halves of the explanation page. */
    public function isInstallation(): bool
    {
        return in_array($this, [
            self::PV, self::VENTILATION, self::HEATING, self::HOT_WATER, self::COOLING, self::SOLAR_THERMAL,
        ], true);
    }
}
