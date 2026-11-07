<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Elements;

use App\Services\SmartTwin\Mapping\Elements\AdviceDetailsBuilder;
use App\Services\SmartTwin\Mapping\Elements\Element;
use App\Services\SmartTwin\Mapping\Elements\ElementReader;
use App\Services\SmartTwin\Mapping\Elements\RoofShape;
use PHPUnit\Framework\TestCase;

/**
 * Every element of the dwelling read from one response, cut down from the first example dossier.
 */
final class ElementReaderTest extends TestCase
{
    private ElementReader $reader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->reader = new ElementReader();
    }

    private function window(float $area, int $glassType = 4, int $frameMaterial = 0): array
    {
        return ['frameType' => 0, 'frameMaterial' => $frameMaterial, 'glassType' => $glassType, 'glassArea' => 0, 'uValue' => 2.9, 'area' => $area, 'id' => "w{$area}"];
    }

    private function properties(float $floorRc = 0.52, bool $heatPump = false): array
    {
        $generators = [['energyCarrier' => 1, 'generatorType' => 5, 'heatPumpSource' => 0, 'heatPumpSourceSystem' => 0, 'id' => 'hr107', 'description' => 'HR107 combiketel']];
        if ($heatPump) {
            array_unshift($generators, ['energyCarrier' => 0, 'generatorType' => 6, 'heatPumpSource' => 4, 'heatPumpSourceSystem' => 4, 'id' => 'hp', 'description' => 'Warmtepomp buitenlucht']);
        }

        return ['properties' => [
            'roofAssemblies' => [
                ['inclination' => 47, 'roofs' => [['roofType' => 2, 'rcValue' => 1.3, 'area' => 39.75, 'id' => 'pitched']], 'windows' => []],
                ['inclination' => 1, 'roofs' => [['roofType' => 0, 'rcValue' => 1.3, 'area' => 30.73, 'id' => 'flat']], 'windows' => []],
            ],
            'facadeAssemblies' => [
                ['facades' => [['facadeType' => 1, 'rcValue' => 1.3, 'area' => 14.25, 'id' => 'f1']],
                 'windows' => [$this->window(2.35), $this->window(2.35), $this->window(1.0, 1, 1)],
                 'doors' => [['insulationInConstruction' => false, 'uValue' => 3.4, 'area' => 2.3, 'id' => 'd1']],
                 'panels' => []],
            ],
            'floorAssemblies' => [[
                'floors' => [['floorType' => 2, 'rcValue' => $floorRc, 'area' => 58.12, 'id' => 'floor']],
                'crawlspaces' => [['floorInsulation' => 0, 'heightAboveGroundLevel' => -0.5, 'ventilation' => true, 'area' => 0, 'id' => 'cs']],
            ]],
            'heatingSystems' => [['individualInstallation' => true, 'generators' => $generators, 'emitterType' => 1, 'id' => 'heating', 'description' => 'HR107 combiketel']],
            'coolingSystems' => [],
            'hotWaterSystems' => [['individualInstallation' => true, 'generatorType' => 13, 'cwClass' => 4, 'isCombiDevice' => true,
                                   'showerHeatRecoveryPresent' => true, 'storageVesselPresent' => false, 'id' => 'tap', 'description' => 'HR107 combiketel']],
            'ventilationSystems' => [['systemType' => 'C1', 'hasHeatRecovery' => false, 'id' => 'vent', 'description' => 'Natuurlijke toevoer en mechanische afvoer']],
            'solarSystems' => [],
            'pvSystems' => [['panelType' => 1, 'numberOfPanels' => 6, 'panelIntegration' => 2, 'surfaceArea' => 9.801, 'wattPeakPerM2' => 165,
                             'inclination' => 15, 'orientation' => 4, 'id' => 'pv']],
        ]];
    }

    private function response(): array
    {
        return ['current' => $this->properties(), 'scenario' => $this->properties(heatPump: true)];
    }

    public function test_the_roof_reads_by_shape(): void
    {
        $roof = $this->reader->read(Element::ROOF, $this->response(), 'current');

        $this->assertSame(['type' => 'pitched', 'rc_value' => 1.3, 'area' => 70.48], $roof->summary);
        $this->assertSame([
            ['type' => 'pitched', 'rc_value' => 1.3, 'area' => 39.75],
            ['type' => 'flat', 'rc_value' => 1.3, 'area' => 30.73],
        ], $roof->lines);

        $flat = $this->reader->read(Element::ROOF, $this->response(), 'current', RoofShape::FLAT);
        $this->assertSame(['flat'], $flat->partIds);
    }

    public function test_the_floor_and_facade_read_their_type_rc_and_surface(): void
    {
        $this->assertSame(['type' => 2, 'rc_value' => 0.52, 'area' => 58.12], $this->reader->read(Element::FLOOR, $this->response(), 'current')->summary);
        $this->assertSame(['type' => 1, 'rc_value' => 1.3, 'area' => 14.25], $this->reader->read(Element::FACADE, $this->response(), 'current')->summary);
    }

    public function test_the_crawlspace_reads_as_present_off_its_height(): void
    {
        $summary = $this->reader->read(Element::CRAWLSPACE, $this->response(), 'current')->summary;

        $this->assertTrue($summary['present']);
        $this->assertSame(-0.5, $summary['height_above_ground_level']);
        $this->assertSame(0, $summary['floor_insulation']);
    }

    public function test_windows_group_by_glass_and_frame(): void
    {
        $windows = $this->reader->read(Element::WINDOWS, $this->response(), 'current');

        $this->assertSame(4, $windows->summary['glass_type']);
        $this->assertSame(5.7, $windows->summary['area']);
        $this->assertSame(3, $windows->summary['count']);
        $this->assertCount(2, $windows->lines);
        $this->assertSame(['glass_type' => 4, 'frame_material' => 0, 'u_value' => 2.9, 'area' => 4.7, 'count' => 2], $windows->lines[0]);
    }

    public function test_an_element_the_dwelling_does_not_have_reads_empty(): void
    {
        $this->assertNull($this->reader->read(Element::PANELS, $this->response(), 'current')->summary);
        $this->assertNull($this->reader->read(Element::COOLING, $this->response(), 'current')->summary);
        $this->assertNull($this->reader->read(Element::SOLAR_THERMAL, $this->response(), 'current')->summary);
    }

    public function test_pv_adds_up_its_peak_power(): void
    {
        $pv = $this->reader->read(Element::PV, $this->response(), 'current');

        // 9,801 m² at 165 Wp/m².
        $this->assertSame(['panel_count' => 6, 'watt_peak' => 1617.0, 'orientation' => 4, 'area' => 9.8], $pv->summary);
    }

    public function test_heating_lists_its_generators(): void
    {
        $scenario = $this->reader->read(Element::HEATING, $this->response(), 'scenario');

        $this->assertSame([6, 5], array_column($scenario->summary['generators'], 'generator_type'));
        $this->assertSame(['heating', 'hp', 'hr107'], $scenario->partIds);
    }

    public function test_installations_keep_their_codes(): void
    {
        $this->assertSame('C1', $this->reader->read(Element::VENTILATION, $this->response(), 'current')->summary['system_type']);
        $this->assertSame(13, $this->reader->read(Element::HOT_WATER, $this->response(), 'current')->summary['generator_type']);
    }

    public function test_a_floor_advice_the_scenario_does_not_carry_out_has_no_after(): void
    {
        // The first example response: a floor solution, a floor that stays at Rc 0,52.
        $details = (new AdviceDetailsBuilder($this->reader))
            ->for($this->response(), ['id' => 'Insulate Ground Floor|x', 'name' => 'Vloerisolatie', 'details' => []], 'floor-insulation', 'floor-insulation')
            ->toArray();

        $this->assertSame(['floor', 'crawlspace'], array_keys($details['elements']));
        $this->assertNull($details['elements']['floor']['after']);
        $this->assertSame(0.0, $details['elements']['floor']['insulated_area']);
        $this->assertSame(['floor'], $details['elements']['floor']['part_ids']);
    }

    public function test_a_heat_pump_advice_shows_the_heating_before_and_after(): void
    {
        $details = (new AdviceDetailsBuilder($this->reader))
            ->for($this->response(), ['id' => 'x', 'name' => 'Hybride warmtepomp', 'details' => []], 'hybrid-heat-pump-outside-air', 'heating')
            ->toArray();

        $this->assertSame([5], array_column($details['elements']['heating']['before']['summary']['generators'], 'generator_type'));
        $this->assertSame([6, 5], array_column($details['elements']['heating']['after']['summary']['generators'], 'generator_type'));
        $this->assertArrayNotHasKey('insulated_area', $details['elements']['heating']);
    }
}
