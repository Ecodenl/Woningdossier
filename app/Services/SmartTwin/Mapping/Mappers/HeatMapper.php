<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Services\SmartTwin\InstallationOptions;
use App\Services\SmartTwin\Mapping\MappingResult;

/**
 * How the dwelling is heated and gets its hot water, now and after the advice
 * (decision 2026-11-07-smarttwin-installaties-mapping, V1–V4, W1–W4, Z1 and I3).
 *
 * Heating, hot water and a solar boiler share their questions, so one mapper decides them all: a
 * solar boiler is a source of heating or hot water, not a question of its own.
 *
 * - heat-source and heat-source-warm-tap-water are multiple choice: every generator and system
 *   counts. What has no option becomes "Anders...", with SmartTwin's description in the -other text.
 * - boiler-type and heat-pump-type have one answer: the worst wins. A heat pump is hybrid when a gas
 *   boiler works beside it in the same system; SmartTwin gives its source. The combinations the
 *   scans have no option for are options only SmartTwin gives (InstallationOptions).
 * - building-heating-application: the emitter of each system.
 * - Collective or individual is not recorded (V2); the generator decides. Placing dates are not in
 *   the response (V4). cwClass is the device's class, not the comfort the resident wants (W1).
 *
 * The new-situation questions get the same from the scenario, as far as they have an option for it:
 * they have no "Anders...".
 */
class HeatMapper extends InstallationMapper
{
    /** HeatingGenerator.energyCarrier. */
    private const GAS = 1;
    private const EXTERNAL_HEAT = [6, 7];

    /** HeatingGenerator.generatorType. */
    private const BOILERS = [1, 2, 3, 4, 5]; // CR, VR, HR100, HR104, HR107: the order of the boiler service value
    private const HEAT_PUMP = 6;
    private const UNKNOWN_GENERATOR = 14;

    /** generatorType of a boiler => new-boiler-type. */
    private const NEW_BOILER_TYPES = [1 => 'conventional', 2 => 'improved-efficiency', 3 => 'hr100', 4 => 'hr104', 5 => 'hr107'];

    /** HeatingSystem.emitterType => building-heating-application, or a label for "Anders...". 0 is none. */
    private const EMITTERS = [1 => 'radiators', 4 => 'air-heating', 5 => 'floor-heating', 6 => 'wall-heating'];
    private const OTHER_EMITTERS = [2 => 'convector', 3 => 'paneelverwarming', 7 => 'plafondverwarming'];

    /** HotWaterSystem.generatorType => heat-source-warm-tap-water (W2). 15 depends on isCombiDevice. */
    private const HOT_WATER = [
        1 => 'hr-boiler', 2 => 'hr-boiler', 3 => 'hr-boiler', 4 => 'hr-boiler', 5 => 'hr-boiler', 6 => 'hr-boiler',
        8 => 'hr-boiler', 9 => 'hr-boiler', 10 => 'hr-boiler', 12 => 'hr-boiler', 13 => 'hr-boiler', 14 => 'hr-boiler',
        7 => 'district-heating',
        11 => 'kitchen-geyser',
        16 => 'heat-pump-boiler',
        17 => 'electric-boiler', 18 => 'electric-boiler', 25 => 'electric-boiler',
    ];
    private const HOT_WATER_HEAT_PUMP = 15;
    private const HOT_WATER_UNKNOWN = 0;

    /** SolarSystem.systemType. */
    private const SOLAR_HOT_WATER = 1;
    private const SOLAR_HOT_WATER_AND_HEATING = 2;

    private const NOTES_HEATING = [
        'individualInstallation' => 'niet vastgelegd: de opwekker beslist, ook bij een collectieve installatie',
        'emitterType'            => 'telt mee in building-heating-application',
        'description'            => 'niet gebruikt: de omschrijving van de opwekker telt',
        'id'                     => 'niet gebruikt',
    ];

    private const NOTES_GENERATOR = [
        'energyCarrier'        => 'telt mee in heat-source en heat-pump-type (hybride)',
        'generatorType'        => 'telt mee in heat-source, boiler-type en heat-pump-type',
        'heatPumpSource'       => 'telt mee in heat-pump-type',
        'heatPumpSourceSystem' => 'niet vastgelegd: collectief of individueel',
        'description'          => 'telt mee in heat-source-other, bij een opwekker zonder eigen optie',
        'id'                   => 'niet gebruikt',
    ];

    private const NOTES_HOT_WATER = [
        'generatorType'             => 'telt mee in heat-source-warm-tap-water',
        'isCombiDevice'             => 'telt mee in heat-source-warm-tap-water (warmtepomp of warmtepompboiler)',
        'description'               => 'telt mee in heat-source-warm-tap-water-other, bij een toestel zonder eigen optie',
        'cwClass'                   => 'niet gebruikt: de klasse van het toestel is niet het comfort van water-comfort',
        'individualInstallation'    => 'niet vastgelegd: collectief of individueel',
        'pipingLengthBathroom'      => 'niet gebruikt: geen vraag',
        'pipingLengthKitchen'       => 'niet gebruikt: geen vraag',
        'showerHeatRecoveryPresent' => 'niet gebruikt: geen vraag',
        'storageVesselPresent'      => 'niet gebruikt: geen vraag',
        'id'                        => 'niet gebruikt',
    ];

    private const NOTES_SOLAR = [
        'systemType'         => 'telt mee in heat-source en heat-source-warm-tap-water (zonneboiler)',
        'isPVT'              => 'niet gebruikt: geen vraag',
        'collectorType'      => 'niet gebruikt: geen vraag',
        'collectorArea'      => 'niet gebruikt: geen vraag',
        'numberOfCollectors' => 'niet gebruikt: geen vraag',
        'inclination'        => 'niet gebruikt: geen vraag in de quickscan',
        'orientation'        => 'niet gebruikt: geen vraag in de quickscan',
        'description'        => 'niet gebruikt',
        'id'                 => 'niet gebruikt',
    ];

    protected function fields(): array
    {
        return [
            'heatingSystems'              => self::NOTES_HEATING,
            'heatingSystems.*.generators' => self::NOTES_GENERATOR,
            'hotWaterSystems'             => self::NOTES_HOT_WATER,
            'solarSystems'                => self::NOTES_SOLAR,
        ];
    }

    protected function fillsScenario(): bool
    {
        return true;
    }

    protected function current(array $response): array
    {
        $heating = $this->heating($response, self::CURRENT);
        $hotWater = $this->hotWater($response, self::CURRENT);

        return [
            ...$this->multipleChoice('heat-source', $heating['sources'], $heating['other'], 'elke opwekker van elk verwarmingssysteem, en een zonneboiler'),
            $this->boilerType($heating),
            $this->heatPumpType($heating),
            ...$this->multipleChoice('building-heating-application', $heating['emitters'], $heating['otherEmitters'], 'de afgifte van elk verwarmingssysteem'),
            ...$this->multipleChoice('heat-source-warm-tap-water', $hotWater['sources'], $hotWater['other'], 'elk warmwatertoestel, en een zonneboiler'),
        ];
    }

    protected function scenario(array $response): array
    {
        $heating = $this->heating($response, self::SCENARIO);
        $hotWater = $this->hotWater($response, self::SCENARIO);
        $note = 'de nieuwe situatie (scenario)';

        $boiler = $heating['boiler'];
        $heatPump = $heating['heatPump'];

        return [
            // No "Anders..." in the new situation: what has no option is left out.
            ...$this->multipleChoice('new-heat-source', array_diff($heating['sources'], ['none']), [], $note, false),
            is_null($boiler)
                ? MappingResult::cleared('new-boiler-type', "{$note}: geen gasketel")
                : MappingResult::mapped('new-boiler-type', self::NEW_BOILER_TYPES[$boiler], $note),
            is_null($heatPump)
                ? MappingResult::cleared('new-heat-pump-type', "{$note}: geen warmtepomp")
                : MappingResult::mapped('new-heat-pump-type', InstallationOptions::HEAT_PUMP_TYPES[$heatPump]['new'], $note),
            ...$this->multipleChoice('new-building-heating-application', array_diff($heating['emitters'], ['none']), [], $note, false),
            ...$this->multipleChoice('new-heat-source-warm-tap-water', array_diff($hotWater['sources'], ['none']), [], $note, false),
        ];
    }

    /**
     * Everything the heating systems say: the sources and emitters with what had no option, the
     * worst boiler (its generatorType) and the worst heat pump (its InstallationOptions order).
     *
     * @return array{sources: array<int, string>, other: array<int, string>, emitters: array<int, string>, otherEmitters: array<int, string>, boiler: ?int, heatPump: ?int, unknownSource: ?int}
     */
    private function heating(array $response, string $section): array
    {
        $result = ['sources' => [], 'other' => [], 'emitters' => [], 'otherEmitters' => [], 'boiler' => null, 'heatPump' => null, 'unknownSource' => null];
        $heatPumps = [];

        foreach ($this->systems($response, $section, 'heatingSystems')->parts as $system) {
            $generators = $system['generators'] ?? [];
            // Hybrid: a boiler or anything on gas works beside the heat pump (V1). A central electric
            // device beside it, as in dossier 100211, leaves it a full heat pump.
            $hasGasBoiler = false;

            foreach ($generators as $generator) {
                if (self::HEAT_PUMP !== ($generator['generatorType'] ?? null)
                    && (in_array($generator['generatorType'] ?? null, self::BOILERS, true) || self::GAS === ($generator['energyCarrier'] ?? null))
                ) {
                    $hasGasBoiler = true;
                }
            }

            foreach ($generators as $generator) {
                $type = $generator['generatorType'] ?? null;
                $carrier = $generator['energyCarrier'] ?? null;

                if (in_array($carrier, self::EXTERNAL_HEAT, true)) {
                    $result['sources'][] = 'district-heating';
                } elseif (self::HEAT_PUMP === $type) {
                    $result['sources'][] = 'heat-pump';
                    $source = $generator['heatPumpSource'] ?? null;
                    $order = is_int($source) ? InstallationOptions::heatPumpOrder($hasGasBoiler, $source) : null;

                    if (is_null($order)) {
                        $result['unknownSource'] = $source;
                    } else {
                        $heatPumps[] = $order;
                    }
                } elseif ($this->isGasBoiler($generator)) {
                    $result['sources'][] = 'hr-boiler';
                    // The worst boiler: CR (1) is worse than HR107 (5).
                    $result['boiler'] = is_null($result['boiler']) ? $type : min($result['boiler'], $type);
                } elseif (self::UNKNOWN_GENERATOR !== $type && ! is_null($type)) {
                    $result['sources'][] = 'none';
                    $result['other'][] = $generator['description'] ?? null;
                }
            }

            $emitter = $system['emitterType'] ?? null;
            if (isset(self::EMITTERS[$emitter])) {
                $result['emitters'][] = self::EMITTERS[$emitter];
            } elseif (isset(self::OTHER_EMITTERS[$emitter])) {
                $result['emitters'][] = 'none';
                $result['otherEmitters'][] = self::OTHER_EMITTERS[$emitter];
            }
        }

        foreach ($this->systems($response, $section, 'solarSystems')->parts as $solar) {
            if (self::SOLAR_HOT_WATER_AND_HEATING === ($solar['systemType'] ?? null)) {
                $result['sources'][] = 'sun-boiler';
            }
        }

        if (! empty($heatPumps)) {
            // The worst heat pump: a hybrid one before a full one, else the first.
            $hybrid = array_values(array_filter($heatPumps, fn (int $order) => InstallationOptions::HEAT_PUMP_TYPES[$order]['hybrid']));
            $result['heatPump'] = $hybrid[0] ?? $heatPumps[0];
        }

        return $result;
    }

    /**
     * @return array{sources: array<int, string>, other: array<int, string>}
     */
    private function hotWater(array $response, string $section): array
    {
        $result = ['sources' => [], 'other' => []];

        foreach ($this->systems($response, $section, 'hotWaterSystems')->parts as $system) {
            $type = $system['generatorType'] ?? null;

            if (self::HOT_WATER_HEAT_PUMP === $type) {
                $result['sources'][] = ($system['isCombiDevice'] ?? false) ? 'heat-pump' : 'heat-pump-boiler';
            } elseif (isset(self::HOT_WATER[$type])) {
                $result['sources'][] = self::HOT_WATER[$type];
            } elseif (self::HOT_WATER_UNKNOWN !== $type && ! is_null($type)) {
                // Wood, pellets and the like (19–24).
                $result['sources'][] = 'none';
                $result['other'][] = $system['description'] ?? null;
            }
        }

        foreach ($this->systems($response, $section, 'solarSystems')->parts as $solar) {
            if (in_array($solar['systemType'] ?? null, [self::SOLAR_HOT_WATER, self::SOLAR_HOT_WATER_AND_HEATING], true)) {
                $result['sources'][] = 'sun-boiler';
            }
        }

        return $result;
    }

    private function boilerType(array $heating): MappingResult
    {
        if (is_null($heating['boiler'])) {
            return MappingResult::cleared('boiler-type', 'geen gasketel');
        }

        $id = $this->elementValues->serviceValueIdForOrder('boiler', $heating['boiler']);

        return is_null($id)
            ? MappingResult::targetMissing('boiler-type', $heating['boiler'], "geen ketel met order {$heating['boiler']}")
            : MappingResult::mapped('boiler-type', $id, "generatorType {$heating['boiler']}, de slechtste gasketel");
    }

    private function heatPumpType(array $heating): MappingResult
    {
        if (is_null($heating['heatPump'])) {
            return is_null($heating['unknownSource'])
                ? MappingResult::cleared('heat-pump-type', 'geen warmtepomp')
                : MappingResult::specViolation('heatPumpSource ' . var_export($heating['unknownSource'], true) . ' staat niet in de spec');
        }

        $order = $heating['heatPump'];
        $id = $this->elementValues->serviceValueIdForOrder('heat-pump', $order);

        if (is_null($id)) {
            // upgrade:add-smart-twin-installation-options has not run on this database.
            return MappingResult::targetMissing('heat-pump-type', $order, "geen warmtepomptype met order {$order}");
        }

        $type = InstallationOptions::HEAT_PUMP_TYPES[$order];

        return MappingResult::mapped('heat-pump-type', $id, ($type['hybrid'] ? 'hybride' : 'volledig') . ", heatPumpSource {$type['source']}");
    }

    private function isGasBoiler(array $generator): bool
    {
        return in_array($generator['generatorType'] ?? null, self::BOILERS, true)
            && self::GAS === ($generator['energyCarrier'] ?? null);
    }
}
