<?php

namespace App\Services\SmartTwin\Mapping;

/**
 * The questions about the dwelling a SmartTwin import fills in, by the part of the dwelling they
 * are about. "Gegevens woning bekijken" shows the dwelling's state from these and no others: the
 * questions SmartTwin does not fill are hidden once a cooperation takes part, so what is in them is
 * left from before SmartTwin, or empty (decision 2026-10-15-smarttwin-gegevens-woning-pagina, S6j).
 *
 * Kept by hand next to the mappers: a mapper that starts filling a question adds it here, under its
 * part, and a new part (windows, an installation) gets a key of its own. SmartTwinImportTest holds
 * this list against what a real import maps, so a question a mapper fills cannot go missing.
 */
final class FilledQuestions
{
    /**
     * Keyed by the part of the dwelling (App\Services\SmartTwin\Mapping\Elements\Element), in the
     * order the page shows them.
     */
    public const BY_PART = [
        'facade' => [
            'current-wall-insulation',
            'has-cavity-wall',
            'wall-surface',
            'insulation-wall-surface',
        ],
        'floor' => [
            'current-floor-insulation',
            'floor-surface',
            'insulation-floor-surface',
            'has-crawlspace',
            'crawlspace-height',
        ],
        'roof' => [
            // The roof shapes are the building_roof_types rows the import writes and removes.
            'current-roof-types',
            'roof-type',
            'current-roof-insulation',
            'is-pitched-roof-insulated',
            'pitched-roof-surface',
            'pitched-roof-insulation-surface',
            'is-flat-roof-insulated',
            'flat-roof-surface',
            'flat-roof-insulation-surface',
        ],
        'windows' => [
            'current-living-rooms-windows',
            'frame-type',
            'total-window-surface',
        ],
        'doors' => [
            'door-quality',
            'total-door-surface',
        ],
        'panels' => [
            'panel-quality',
            'panel-frame-type',
            'total-panel-surface',
        ],
        // A solar boiler is an answer of heating and hot water, not a part of its own.
        'heating' => [
            'heat-source',
            'heat-source-other',
            'boiler-type',
            'heat-pump-type',
            'building-heating-application',
            'building-heating-application-other',
        ],
        'hot-water' => [
            'heat-source-warm-tap-water',
            'heat-source-warm-tap-water-other',
        ],
        'ventilation' => [
            'ventilation-type',
            'ventilation-heat-recovery',
        ],
        'pv' => [
            'has-solar-panels',
            'solar-panel-count',
            'total-installed-power',
            'solar-panel-peak-power',
            'solar-panel-angle',
            'solar-panel-orientation',
        ],
    ];

    /**
     * Filled, but not shown. The sleeping rooms get the same glass as the living rooms (GlazingMapper),
     * and the page would only say it twice. The new-situation questions (HeatMapper) describe the
     * dwelling after the advice, not as it is.
     */
    public const NOT_SHOWN = [
        'current-sleeping-rooms-windows',
        'new-heat-source',
        'new-boiler-type',
        'new-heat-pump-type',
        'new-building-heating-application',
        'new-heat-source-warm-tap-water',
    ];

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return array_merge(...array_values(self::BY_PART));
    }
}
