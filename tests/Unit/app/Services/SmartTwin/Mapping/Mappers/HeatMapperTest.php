<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Enums\SmartTwin\MappingStatus;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Mappers\HeatMapper;
use PHPUnit\Framework\TestCase;

/**
 * Heating, hot water and the solar boiler. The figures come from dossier 100211, a coach's advice:
 * a free standing gas stove becoming an outside air heat pump with a central electric heater, and the
 * example response: an HR107 combi boiler becoming a hybrid heat pump.
 */
final class HeatMapperTest extends TestCase
{
    use MapsInstallations;

    private HeatMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = new HeatMapper(new FakeElementValues());
    }

    protected function mapper(): FieldMapper
    {
        return $this->mapper;
    }

    private function generator(int $type, int $carrier, int $source = 0, string $description = ''): array
    {
        return ['energyCarrier' => $carrier, 'generatorType' => $type, 'heatPumpSource' => $source,
                'heatPumpSourceSystem' => 0, 'description' => $description, 'id' => uniqid()];
    }

    private function heating(array $generators, int $emitter = 1): array
    {
        return ['individualInstallation' => true, 'generators' => $generators, 'emitterType' => $emitter,
                'description' => 'klimaatsysteem verwarming', 'id' => uniqid()];
    }

    private function hotWater(int $type, bool $combi = false, string $description = ''): array
    {
        return ['individualInstallation' => true, 'pipingLengthBathroom' => 7, 'pipingLengthKitchen' => 9, 'generatorType' => $type,
                'cwClass' => 4, 'isCombiDevice' => $combi, 'showerHeatRecoveryPresent' => false, 'storageVesselPresent' => false,
                'description' => $description, 'id' => uniqid()];
    }

    private function boiler(int $order): int
    {
        return FakeElementValues::SERVICE_OFFSET + FakeElementValues::SERVICES['boiler'] + $order;
    }

    private function heatPump(int $order): int
    {
        return FakeElementValues::SERVICE_OFFSET + FakeElementValues::SERVICES['heat-pump'] + $order;
    }

    public function test_dossier_100211(): void
    {
        $answers = $this->answers($this->response(
            [
                'heatingSystems'  => [$this->heating([$this->generator(0, 1, 0, 'gaskachel vrijstaand')], 0)],
                'hotWaterSystems' => [$this->hotWater(9)],
            ],
            [
                'heatingSystems'  => [$this->heating([$this->generator(6, 0, 4, 'Combiwarmtepomp buitenlucht'), $this->generator(13, 0, 0, 'Elektrische bijverwarmer')])],
                'hotWaterSystems' => [$this->hotWater(15, true)],
            ],
        ));

        $this->assertSame([
            // A local heater has an option of its own (HD-21).
            'heat-source'                        => ['local-heating'],
            'heat-source-other'                  => null,
            'boiler-type'                        => null,
            'heat-pump-type'                     => null,
            'building-heating-application'       => null,
            'building-heating-application-other' => null,
            'heat-source-warm-tap-water'         => ['hr-boiler'],
            'heat-source-warm-tap-water-other'   => null,
            // A central electric heater beside the heat pump is no boiler: a full heat pump.
            'new-heat-source'                    => ['heat-pump'],
            'new-boiler-type'                    => null,
            'new-heat-pump-type'                 => 'full-heat-pump-outside-air',
            'new-building-heating-application'   => ['radiators'],
            'new-heat-source-warm-tap-water'     => ['heat-pump'],
        ], $answers);
    }

    public function test_the_electric_backup_heater_beside_a_heat_pump_is_left_out(): void
    {
        // Dossier 100281: an outside air heat pump with an electric backup heater, on floor heating.
        $answers = $this->answers($this->response([
            'heatingSystems' => [$this->heating([
                $this->generator(6, 0, 4, 'combiwarmtepomp buitenlucht-water zonder koeling'),
                $this->generator(13, 0, 0, 'elektrische bijverwarmer elektrische doorstroom'),
            ], 5)],
        ]));

        $this->assertSame(['heat-pump'], $answers['heat-source']);
        $this->assertNull($answers['heat-source-other']);
        $this->assertSame($this->heatPump(4), $answers['heat-pump-type']); // Volledige warmtepomp buitenlucht
        $this->assertSame(['floor-heating'], $answers['building-heating-application']);
    }

    public function test_a_stove_beside_a_heat_pump_stays(): void
    {
        $answers = $this->answers($this->response([
            'heatingSystems' => [$this->heating([
                $this->generator(6, 0, 4, 'combiwarmtepomp buitenlucht'),
                $this->generator(13, 0, 0, 'elektrische bijverwarmer'),
                $this->generator(8, 3, 0, 'houtkachel'),
            ])],
        ]));

        $this->assertSame(['heat-pump', 'biomass'], $answers['heat-source']);
        $this->assertNull($answers['heat-source-other']);
    }

    public function test_the_client_mapping_of_the_other_heaters(): void
    {
        // HD-21: 0 is a local heater, 7–12 biomass, 14 and 15 "Anders..." with "onbekend".
        // On electricity (0), so that only the type decides; a biomass carrier has a test of its own.
        foreach ([0 => ['local-heating'], 7 => ['biomass'], 9 => ['biomass'], 12 => ['biomass'], 14 => ['none'], 15 => ['none']] as $type => $expected) {
            $answers = $this->answers($this->response([
                'heatingSystems' => [$this->heating([$this->generator($type, 0, 0, 'omschrijving')])],
            ]));

            $this->assertSame($expected, $answers['heat-source'], "generatorType {$type}");
            $this->assertSame(['none'] === $expected ? 'onbekend' : null, $answers['heat-source-other'], "generatorType {$type}");
        }

        // Without a counterpart in the new situation, they are left out there.
        $answers = $this->answers($this->response([], [
            'heatingSystems' => [$this->heating([$this->generator(0, 1), $this->generator(11, 3)])],
        ]));
        $this->assertNull($answers['new-heat-source']);
    }

    public function test_a_generator_on_biomass_is_biomass_whatever_its_type(): void
    {
        // Dossier 100279 (HD-26): the resident scan sends a biomass combi boiler as a central device on
        // "biomass other", and its hot water as a pellet stove (21).
        $answers = $this->answers($this->response([
            'heatingSystems'  => [$this->heating([$this->generator(13, 5, 0, 'Biomassa combiketel')])],
            'hotWaterSystems' => [$this->hotWater(21, true, 'Biomassa combiketel')],
        ]));

        $this->assertSame(['biomass'], $answers['heat-source']);
        $this->assertNull($answers['heat-source-other']);

        // Every biomass carrier counts, and so does an unknown or other generator burning it.
        foreach ([[13, 3], [13, 4], [14, 5], [15, 4]] as [$type, $carrier]) {
            $answers = $this->answers($this->response([
                'heatingSystems' => [$this->heating([$this->generator($type, $carrier, 0, 'omschrijving')])],
            ]));

            $this->assertSame(['biomass'], $answers['heat-source'], "generatorType {$type}, energyCarrier {$carrier}");
            $this->assertNull($answers['heat-source-other'], "generatorType {$type}, energyCarrier {$carrier}");
        }

        // The client's mapping of a local heater stands, whatever it burns.
        $answers = $this->answers($this->response([
            'heatingSystems' => [$this->heating([$this->generator(0, 5)])],
        ]));
        $this->assertSame(['local-heating'], $answers['heat-source']);
    }

    public function test_a_central_device_on_biomass_beside_a_heat_pump_is_no_backup_heater(): void
    {
        $answers = $this->answers($this->response([
            'heatingSystems' => [$this->heating([
                $this->generator(6, 0, 4, 'combiwarmtepomp buitenlucht'),
                $this->generator(13, 5, 0, 'Biomassa combiketel'),
            ])],
        ]));

        $this->assertSame(['heat-pump', 'biomass'], $answers['heat-source']);
    }

    public function test_a_central_electric_heater_without_a_heat_pump_stays(): void
    {
        $answers = $this->answers($this->response([
            'heatingSystems' => [
                $this->heating([$this->generator(6, 0, 4, 'combiwarmtepomp buitenlucht')]),
                $this->heating([$this->generator(13, 0, 0, 'elektrische cv-ketel')]),
            ],
        ]));

        $this->assertSame(['heat-pump', 'none'], $answers['heat-source']);
        $this->assertSame('elektrische cv-ketel', $answers['heat-source-other']);
    }

    public function test_a_gas_boiler_beside_the_heat_pump_makes_it_hybrid(): void
    {
        $answers = $this->answers($this->response([
            'heatingSystems' => [$this->heating([$this->generator(6, 0, 4), $this->generator(5, 1)])],
        ]));

        $this->assertSame(['heat-pump', 'hr-boiler'], $answers['heat-source']);
        $this->assertSame($this->boiler(5), $answers['boiler-type']);
        $this->assertSame($this->heatPump(1), $answers['heat-pump-type']); // Hybride met buitenlucht
        $this->assertSame('hybrid-heat-pump-outside-air', $answers['new-heat-pump-type']);
        $this->assertSame('hr107', $answers['new-boiler-type']);
    }

    public function test_a_combination_the_scans_lack_is_an_option_only_smart_twin_gives(): void
    {
        // A full heat pump on ventilation air: order 12, added for SmartTwin.
        $answers = $this->answers($this->response([
            'heatingSystems' => [$this->heating([$this->generator(6, 0, 5)])],
        ]));

        $this->assertSame($this->heatPump(12), $answers['heat-pump-type']);
        $this->assertSame('full-heat-pump-ventilation-air', $answers['new-heat-pump-type']);
    }

    public function test_the_worst_boiler_and_heat_pump_win(): void
    {
        $answers = $this->answers($this->response([
            'heatingSystems' => [
                $this->heating([$this->generator(5, 1)]),
                $this->heating([$this->generator(2, 1)]),
                $this->heating([$this->generator(6, 0, 1)]),                         // full, ground
                $this->heating([$this->generator(6, 0, 4), $this->generator(3, 1)]), // hybrid, outside air
            ],
        ]));

        $this->assertSame($this->boiler(2), $answers['boiler-type']); // VR before HR107
        $this->assertSame($this->heatPump(1), $answers['heat-pump-type']); // hybrid before full
    }

    public function test_external_heat_is_district_heating_and_collective_is_not_recorded(): void
    {
        $system = $this->heating([$this->generator(15, 6)]);
        $system['individualInstallation'] = false;

        $answers = $this->answers($this->response(['heatingSystems' => [$system], 'hotWaterSystems' => [$this->hotWater(7)]]));

        $this->assertSame(['district-heating'], $answers['heat-source']);
        $this->assertSame(['district-heating'], $answers['heat-source-warm-tap-water']);
    }

    public function test_emitters_without_an_option_become_anders(): void
    {
        $answers = $this->answers($this->response([
            'heatingSystems' => [$this->heating([$this->generator(5, 1)], 5), $this->heating([$this->generator(5, 1)], 2)],
        ]));

        $this->assertSame(['floor-heating', 'none'], $answers['building-heating-application']);
        $this->assertSame('convector', $answers['building-heating-application-other']);
        $this->assertSame(['floor-heating'], $answers['new-building-heating-application']);
    }

    public function test_hot_water_is_grouped_as_decided(): void
    {
        $expected = [
            1 => 'hr-boiler', 6 => 'hr-boiler', 9 => 'hr-boiler', 13 => 'hr-boiler', 14 => 'hr-boiler',
            7 => 'district-heating', 11 => 'kitchen-geyser', 16 => 'heat-pump-boiler',
            17 => 'electric-boiler', 18 => 'electric-boiler', 25 => 'electric-boiler',
        ];

        foreach ($expected as $type => $answer) {
            $answers = $this->answers($this->response(['hotWaterSystems' => [$this->hotWater($type)]]));

            $this->assertSame([$answer], $answers['heat-source-warm-tap-water'], "generatorType {$type}");
        }

        $this->assertSame(['heat-pump'], $this->answers($this->response(['hotWaterSystems' => [$this->hotWater(15, true)]]))['heat-source-warm-tap-water']);
        $this->assertSame(['heat-pump-boiler'], $this->answers($this->response(['hotWaterSystems' => [$this->hotWater(15, false)]]))['heat-source-warm-tap-water']);

        $wood = $this->answers($this->response(['hotWaterSystems' => [$this->hotWater(19, false, 'houtkachel')]]));
        $this->assertSame(['none'], $wood['heat-source-warm-tap-water']);
        $this->assertSame('houtkachel', $wood['heat-source-warm-tap-water-other']);
        $this->assertNull($wood['new-heat-source-warm-tap-water']); // no "Anders..." in the new situation

        $this->assertNull($this->answers($this->response(['hotWaterSystems' => [$this->hotWater(0)]]))['heat-source-warm-tap-water']);
    }

    public function test_a_solar_boiler_is_a_source_of_hot_water_and_maybe_heating(): void
    {
        $solar = fn (int $type) => ['systemType' => $type, 'isPVT' => false, 'collectorType' => 1, 'collectorArea' => 4,
                                    'numberOfCollectors' => 2, 'inclination' => 45, 'orientation' => 5, 'description' => '', 'id' => uniqid()];
        $heating = [$this->heating([$this->generator(5, 1)])];
        $hotWater = [$this->hotWater(13)];

        $none = $this->answers($this->response(['heatingSystems' => $heating, 'hotWaterSystems' => $hotWater, 'solarSystems' => [$solar(0)]]));
        $this->assertSame(['hr-boiler'], $none['heat-source']);
        $this->assertSame(['hr-boiler'], $none['heat-source-warm-tap-water']);

        $water = $this->answers($this->response(['heatingSystems' => $heating, 'hotWaterSystems' => $hotWater, 'solarSystems' => [$solar(1)]]));
        $this->assertSame(['hr-boiler'], $water['heat-source']);
        $this->assertSame(['hr-boiler', 'sun-boiler'], $water['heat-source-warm-tap-water']);

        $both = $this->answers($this->response(['heatingSystems' => $heating, 'hotWaterSystems' => $hotWater, 'solarSystems' => [$solar(2)]]));
        $this->assertSame(['hr-boiler', 'sun-boiler'], $both['heat-source']);
        $this->assertSame(['hr-boiler', 'sun-boiler'], $both['heat-source-warm-tap-water']);
    }

    public function test_an_unknown_heat_pump_source_is_a_spec_violation(): void
    {
        $response = $this->response(['heatingSystems' => [$this->heating([$this->generator(6, 0, 9)])]]);

        $this->assertNotEmpty($this->withStatus($response, MappingStatus::SPEC_VIOLATION));
        $this->assertArrayNotHasKey('heat-pump-type', $this->answers($response));
    }

    public function test_the_unused_fields_are_reported_with_a_reason(): void
    {
        $notes = array_map(
            fn ($result) => $result->note,
            $this->withStatus($this->response(['hotWaterSystems' => [$this->hotWater(9)]]), MappingStatus::SKIPPED),
        );

        $this->assertContains('niet gebruikt: de klasse van het toestel is niet het comfort van water-comfort', $notes);
    }
}
