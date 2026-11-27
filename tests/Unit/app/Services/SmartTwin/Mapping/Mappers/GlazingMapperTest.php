<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Enums\GlassType;
use App\Enums\SmartTwin\MappingStatus;
use App\Models\Building;
use App\Services\SmartTwin\Mapping\Mappers\GlazingMapper;
use App\Services\SmartTwin\Mapping\MappingResult;
use App\Services\SmartTwin\Mapping\ResponseFlattener;
use PHPUnit\Framework\TestCase;

/**
 * The figures of dossier 100211, a coach's advice: thirteen windows of 15,48 m² double and
 * 12,09 m² single glass, all in wooden frames, and four uninsulated doors of 10,92 m² together.
 */
final class GlazingMapperTest extends TestCase
{
    private GlazingMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = new GlazingMapper(new FakeElementValues());
    }

    private function window(float $area, int $glassType, int $frameType = 0): array
    {
        return ['frameType' => $frameType, 'frameMaterial' => 0, 'glassType' => $glassType, 'glassArea' => 0,
                'uValue' => 2.9, 'area' => $area, 'id' => uniqid(), 'description' => 'Kozijn'];
    }

    private function door(float $area, bool $insulated): array
    {
        return ['insulationInConstruction' => $insulated, 'uValue' => 3.4, 'area' => $area, 'id' => uniqid(), 'description' => 'Deur'];
    }

    private function panel(float $area, int $insulationPresent, int $frameType): array
    {
        return ['insulationPresent' => $insulationPresent, 'frameType' => $frameType, 'uValue' => 1.0, 'area' => $area,
                'id' => uniqid(), 'description' => 'Paneel'];
    }

    /**
     * @param  array<int, array<string, mixed>>  $facade  The parts of one facade assembly, by kind.
     * @param  array<int, array<string, mixed>>  $roofWindows
     */
    private function response(array $facade, array $roofWindows = [], ?array $scenarioFacade = null): array
    {
        $section = fn (array $facade) => ['properties' => [
            'facadeAssemblies' => [array_merge(['windows' => [], 'doors' => [], 'panels' => [], 'facades' => [], 'id' => 'fa'], $facade)],
            'roofAssemblies'   => [['windows' => $roofWindows, 'roofs' => [], 'id' => 'ra']],
        ]];

        return ['current' => $section($facade), 'scenario' => $section($scenarioFacade ?? $facade)];
    }

    /** @return array<int, MappingResult> */
    private function mapClaimed(array $response): array
    {
        $claimed = array_flip($this->mapper->paths());
        $results = [];

        foreach ((new ResponseFlattener())->flatten($response) as $leaf) {
            if (isset($claimed[$leaf->pathGroup])) {
                $result = $this->mapper->map(new Building(), $leaf, $response);
                array_push($results, ...(is_array($result) ? $result : [$result]));
            }
        }

        return $results;
    }

    /** @return array<string, mixed> target => value */
    private function answers(array $response): array
    {
        $answers = [];

        foreach ($this->mapClaimed($response) as $result) {
            if (MappingStatus::MAPPED === $result->status) {
                $this->assertArrayNotHasKey($result->target, $answers, "{$result->target} is answered twice");
                $answers[$result->target] = $result->value;
            }
        }

        return $answers;
    }

    private function glass(GlassType $glassType): int
    {
        return FakeElementValues::ORDER_OFFSET + $glassType->value;
    }

    private function frame(int $order): int
    {
        return FakeElementValues::ORDER_OFFSET + $order;
    }

    private function dossier100211(): array
    {
        $windows = [];
        foreach ([2.35, 2.35, 2.35, 2.35, 2.35, 1.86, 1.87] as $area) {
            $windows[] = $this->window($area, 4); // 15,48 m² standard double
        }
        foreach ([2.35, 2.35, 2.35, 2.35, 1.35, 1.34] as $area) {
            $windows[] = $this->window($area, 5); // 12,09 m² single
        }

        return [
            'windows' => $windows,
            'doors'   => [$this->door(2.3, false), $this->door(2.3, false), $this->door(2.28, false), $this->door(4.04, false)],
        ];
    }

    public function test_dossier_100211_as_the_customer_described(): void
    {
        $answers = $this->answers($this->response($this->dossier100211()));

        $this->assertSame([
            'total-window-surface'           => 27.57,
            'current-living-rooms-windows'   => $this->glass(GlassType::DOUBLE),
            'current-sleeping-rooms-windows' => $this->glass(GlassType::DOUBLE),
            'frame-type'                     => $this->frame(5), // Hout
            'total-door-surface'             => 10.92,
            'door-quality'                   => 'not-insulated',
            'total-panel-surface'            => null,
            'panel-quality'                  => null,
            'panel-frame-type'               => null,
        ], $answers);
    }

    public function test_every_glass_type_lands_on_its_own_option(): void
    {
        $expected = [
            0 => GlassType::TRIPLE, 1 => GlassType::HR_PLUS_PLUS, 2 => GlassType::HR_PLUS, 3 => GlassType::HR,
            4 => GlassType::DOUBLE, 5 => GlassType::SINGLE, 6 => GlassType::VACUUM,
        ];

        foreach ($expected as $code => $glassType) {
            $answers = $this->answers($this->response(['windows' => [$this->window(2.0, $code)]]));

            $this->assertSame($this->glass($glassType), $answers['current-living-rooms-windows'], "glassType {$code}");
        }
    }

    public function test_roof_windows_count_as_windows(): void
    {
        // 2 m² HR++ in the facade, 3 m² triple in the roof: the roof decides.
        $answers = $this->answers($this->response(['windows' => [$this->window(2.0, 1)]], [$this->window(3.0, 0, 1)]));

        $this->assertSame(5.0, $answers['total-window-surface']);
        $this->assertSame($this->glass(GlassType::TRIPLE), $answers['current-living-rooms-windows']);
        $this->assertSame($this->frame(6), $answers['frame-type']); // Kunststof
    }

    public function test_on_a_tie_the_worst_wins(): void
    {
        $answers = $this->answers($this->response([
            'windows' => [$this->window(2.0, 6, 0), $this->window(2.0, 3, 3)],
            'doors'   => [$this->door(2.0, true), $this->door(2.0, false)],
            'panels'  => [$this->panel(1.0, 0, 0), $this->panel(1.0, 2, 2)],
        ]));

        $this->assertSame($this->glass(GlassType::HR), $answers['current-living-rooms-windows']);
        $this->assertSame($this->frame(8), $answers['frame-type']); // Aluminium niet thermisch gescheiden
        $this->assertSame('not-insulated', $answers['door-quality']);
        $this->assertSame('unknown', $answers['panel-quality']);
        $this->assertSame('metal', $answers['panel-frame-type']);
    }

    public function test_panels_give_their_surface_quality_and_frame(): void
    {
        $answers = $this->answers($this->response([
            'panels' => [$this->panel(3.0, 1, 1), $this->panel(1.5, 0, 0)],
        ]));

        $this->assertSame(4.5, $answers['total-panel-surface']);
        $this->assertSame('not-insulated', $answers['panel-quality']);
        $this->assertSame('metal-thermally-broken', $answers['panel-frame-type']);
    }

    public function test_the_scenario_is_not_mapped(): void
    {
        // As in 100211: the scenario has vacuum glass everywhere, the dwelling has not.
        $facade = $this->dossier100211();
        $scenario = $facade;
        $scenario['windows'] = array_map(fn (array $window) => array_merge($window, ['glassType' => 6]), $facade['windows']);

        $answers = $this->answers($this->response($facade, [], $scenario));

        $this->assertSame($this->glass(GlassType::DOUBLE), $answers['current-living-rooms-windows']);
    }

    public function test_the_unused_fields_are_reported_with_a_reason(): void
    {
        $results = $this->mapClaimed($this->response($this->dossier100211()));
        $skipped = array_filter($results, fn (MappingResult $result) => MappingStatus::SKIPPED === $result->status);
        $notes = array_map(fn (MappingResult $result) => $result->note, $skipped);

        $this->assertContains('niet gebruikt: area (glas plus kozijn) telt', $notes);
        $this->assertContains('niet gebruikt: frameType geeft het kozijn', $notes);
        $this->assertContains('niet gebruikt: het scenario beschrijft de woning na het advies, zie de details op de kaart', $notes);
    }

    public function test_an_unknown_glass_type_is_a_spec_violation(): void
    {
        $results = $this->mapClaimed($this->response(['windows' => [$this->window(2.0, 9)]]));

        $this->assertNotEmpty(array_filter($results, fn (MappingResult $result) => MappingStatus::SPEC_VIOLATION === $result->status));
        $this->assertArrayNotHasKey('current-living-rooms-windows', $this->answers($this->response(['windows' => [$this->window(2.0, 9)]])));
    }
}
