<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Mappers\PvMapper;
use PHPUnit\Framework\TestCase;

/**
 * The figures of dossier 100261: nine panels of 14,7015 m² at 165 Wp/m² facing south-west and three
 * of 4,8 m² at 175 Wp/m² facing south, both at 30°.
 */
final class PvMapperTest extends TestCase
{
    use MapsInstallations;

    private PvMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = new PvMapper(new FakeElementValues());
    }

    protected function mapper(): FieldMapper
    {
        return $this->mapper;
    }

    private function system(int $panels, float $surface, float $wattPeak, int $orientation, int $inclination = 30): array
    {
        return ['panelType' => 1, 'numberOfPanels' => $panels, 'panelIntegration' => 2, 'surfaceArea' => $surface,
                'wattPeakPerM2' => $wattPeak, 'inclination' => $inclination, 'orientation' => $orientation,
                'description' => '', 'id' => uniqid()];
    }

    private function orientation(int $code): int
    {
        return FakeElementValues::ORIENTATION_OFFSET + $code;
    }

    public function test_dossier_100261(): void
    {
        $answers = $this->answers($this->response(['pvSystems' => [$this->system(9, 14.7015, 165, 6), $this->system(3, 4.8, 175, 5)]]));

        $this->assertSame([
            'has-solar-panels'        => 'yes',
            'solar-panel-count'       => 12,
            'total-installed-power'   => 3266,
            'solar-panel-peak-power'  => 272,
            'solar-panel-angle'       => 30,
            'solar-panel-orientation' => $this->orientation(6), // the larger system faces south-west
        ], $answers);
    }

    public function test_flat_and_north_facing_panels_get_the_options_only_smart_twin_gives(): void
    {
        $this->assertSame($this->orientation(0), $this->answers($this->response(['pvSystems' => [$this->system(6, 10, 160, 0, 0)]]))['solar-panel-orientation']);
        $this->assertSame($this->orientation(1), $this->answers($this->response(['pvSystems' => [$this->system(6, 10, 160, 1)]]))['solar-panel-orientation']);
    }

    public function test_no_panels_is_no(): void
    {
        $this->assertSame([
            'has-solar-panels'        => 'no',
            'solar-panel-count'       => null,
            'total-installed-power'   => null,
            'solar-panel-peak-power'  => null,
            'solar-panel-angle'       => null,
            'solar-panel-orientation' => null,
        ], $this->answers($this->response([])));
    }
}
