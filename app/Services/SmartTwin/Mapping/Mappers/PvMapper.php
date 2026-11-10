<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Services\SmartTwin\InstallationOptions;
use App\Services\SmartTwin\Mapping\MappingResult;

/**
 * The solar panels of the dwelling as it is (decision 2026-11-07-smarttwin-installaties-mapping,
 * P1–P4 and I2).
 *
 * - has-solar-panels: yes when SmartTwin lists a system, no when it lists none. SmartTwin describes
 *   the whole dwelling, so an empty list says there are none (unlike the crawlspace).
 * - solar-panel-count and total-installed-power: added up over the systems; the power is surface
 *   times Wp per m².
 * - solar-panel-angle and solar-panel-orientation: those of the system with the largest surface,
 *   as one answer covers the whole dwelling. Horizontal and north are options only SmartTwin gives.
 * - solar-panel-peak-power: not given by SmartTwin; the total power divided by the number of panels,
 *   which keeps it in line with the two answers above.
 */
class PvMapper extends InstallationMapper
{
    protected function fields(): array
    {
        return [
            'pvSystems' => [
                'numberOfPanels'   => 'telt mee in solar-panel-count en solar-panel-peak-power',
                'surfaceArea'      => 'telt mee in total-installed-power, en kiest het systeem voor hoek en oriëntatie',
                'wattPeakPerM2'    => 'telt mee in total-installed-power',
                'inclination'      => 'telt mee in solar-panel-angle',
                'orientation'      => 'telt mee in solar-panel-orientation',
                'panelType'        => 'niet gebruikt: geen vraag',
                'panelIntegration' => 'niet gebruikt: geen vraag',
                'description'      => 'niet gebruikt',
                'id'               => 'niet gebruikt',
            ],
        ];
    }

    protected function current(array $response): array
    {
        $systems = $this->systems($response, self::CURRENT, 'pvSystems');

        if (0 === $systems->count()) {
            $note = 'geen zonnepanelen in de response';

            return [
                MappingResult::mapped('has-solar-panels', 'no', $note),
                MappingResult::cleared('solar-panel-count', $note),
                MappingResult::cleared('total-installed-power', $note),
                MappingResult::cleared('solar-panel-peak-power', $note),
                MappingResult::cleared('solar-panel-angle', $note),
                MappingResult::cleared('solar-panel-orientation', $note),
            ];
        }

        $count = (int) array_sum(array_map(fn (array $system) => (int) ($system['numberOfPanels'] ?? 0), $systems->parts));
        $power = (int) round(array_sum(array_map(
            fn (array $system) => (float) ($system['surfaceArea'] ?? 0) * (float) ($system['wattPeakPerM2'] ?? 0),
            $systems->parts,
        )));

        $largest = $systems->parts[0];
        foreach ($systems->parts as $system) {
            if ((float) ($system['surfaceArea'] ?? 0) > (float) ($largest['surfaceArea'] ?? 0)) {
                $largest = $system;
            }
        }

        $systemsNote = "{$systems->count()} systeem/systemen";

        return [
            MappingResult::mapped('has-solar-panels', 'yes', $systemsNote),
            MappingResult::mapped('solar-panel-count', $count, "som over {$systemsNote}"),
            MappingResult::mapped('total-installed-power', $power, "oppervlak × Wp/m², opgeteld over {$systemsNote}"),
            $count > 0
                ? MappingResult::mapped('solar-panel-peak-power', (int) round($power / $count), 'totaal vermogen gedeeld door het aantal panelen')
                : MappingResult::cleared('solar-panel-peak-power', 'geen aantal panelen'),
            is_null($largest['inclination'] ?? null)
                ? MappingResult::cleared('solar-panel-angle', 'geen hoek bij het grootste systeem')
                : MappingResult::mapped('solar-panel-angle', (int) round((float) $largest['inclination']), 'van het systeem met het grootste oppervlak'),
            $this->orientation($largest['orientation'] ?? null),
        ];
    }

    private function orientation(mixed $code): MappingResult
    {
        $short = is_int($code) ? (InstallationOptions::ORIENTATIONS[$code]['short'] ?? null) : null;

        if (is_null($short)) {
            return MappingResult::specViolation('orientation ' . var_export($code, true) . ' staat niet in de spec');
        }

        $id = $this->elementValues->orientationIdFor($short);

        return is_null($id)
            // upgrade:add-smart-twin-installation-options has not run on this database.
            ? MappingResult::targetMissing('solar-panel-orientation', $short, "geen oriëntatie '{$short}'")
            : MappingResult::mapped('solar-panel-orientation', $id, "orientation {$code} van het systeem met het grootste oppervlak");
    }
}
