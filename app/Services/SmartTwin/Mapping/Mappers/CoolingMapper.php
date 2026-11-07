<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Services\SmartTwin\Mapping\MappingResult;

/**
 * The cooling of the dwelling as it is, into the question only SmartTwin fills
 * (decision 2026-11-07-smarttwin-installaties-mapping, K1). Every system counts, as a dwelling can
 * cool in more than one way; none listed is "Geen koeling", as SmartTwin describes the whole dwelling.
 */
class CoolingMapper extends InstallationMapper
{
    /** SmartTwin's generatorType => cooling-type. */
    private const TYPES = [
        0 => 'unknown',
        1 => 'compression',
        2 => 'absorption',
        3 => 'external-cold',
        4 => 'free-cooling',
        5 => 'dew-point',
    ];

    protected function fields(): array
    {
        return [
            'coolingSystems' => [
                'generatorType'  => 'telt mee in cooling-type',
                'energyCarrier'  => 'niet gebruikt: geen vraag',
                'heatPumpSource' => 'niet gebruikt: geen vraag',
                'description'    => 'niet gebruikt',
                'id'             => 'niet gebruikt',
            ],
        ];
    }

    protected function current(array $response): array
    {
        $systems = $this->systems($response, self::CURRENT, 'coolingSystems');

        if (0 === $systems->count()) {
            return [MappingResult::mapped('cooling-type', ['none'], 'geen koeling in de response')];
        }

        $types = [];
        foreach ($systems->parts as $system) {
            $type = self::TYPES[$system['generatorType'] ?? -1] ?? null;

            if (is_null($type)) {
                return [MappingResult::specViolation('generatorType ' . var_export($system['generatorType'] ?? null, true) . ' staat niet in de spec')];
            }

            $types[] = $type;
        }

        return [MappingResult::mapped('cooling-type', array_values(array_unique($types)), "{$systems->count()} koelsysteem/-systemen")];
    }
}
