<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Enums\SmartTwin\MappingStatus;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Mappers\VentilationMapper;
use PHPUnit\Framework\TestCase;

/**
 * The codes and descriptions come from the real responses: A1 (100211, 100271), C1 (the example),
 * D5c (100271 after the advice), E1 "Decentrale ventilatie met WTW" (100211 after the advice) and
 * A.1 (100261).
 */
final class VentilationMapperTest extends TestCase
{
    use MapsInstallations;

    private VentilationMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = new VentilationMapper(new FakeElementValues());
    }

    protected function mapper(): FieldMapper
    {
        return $this->mapper;
    }

    private function system(string $type, bool $heatRecovery = false): array
    {
        return ['systemType' => $type, 'hasHeatRecovery' => $heatRecovery, 'description' => '', 'id' => uniqid()];
    }

    private function type(int $order): int
    {
        return FakeElementValues::SERVICE_OFFSET + FakeElementValues::SERVICES['house-ventilation'] + $order;
    }

    public function test_the_letter_decides_the_kind(): void
    {
        $expected = ['A1' => 1, 'A.1' => 1, 'B2' => 2, 'C1' => 2, 'C4a' => 2, 'D5c' => 3, 'E1' => 4];

        foreach ($expected as $code => $order) {
            $answers = $this->answers($this->response(['ventilationSystems' => [$this->system($code)]]));

            $this->assertSame($this->type($order), $answers['ventilation-type'], $code);
        }
    }

    public function test_b_is_reported_as_an_approximation(): void
    {
        $mapped = $this->withStatus($this->response(['ventilationSystems' => [$this->system('B1')]]), MappingStatus::MAPPED);

        $this->assertStringContainsString('benaderd als Mechanische ventilatie', $mapped[0]->note);
    }

    public function test_heat_recovery_and_demand_driven(): void
    {
        $answers = $this->answers($this->response(['ventilationSystems' => [$this->system('E1', true)]]));

        $this->assertSame(1, $answers['ventilation-heat-recovery']);
        // Left alone until SmartTwin explains the codes.
        $this->assertArrayNotHasKey('ventilation-demand-driven', $answers);
    }

    public function test_with_more_than_one_system_the_worst_wins(): void
    {
        $answers = $this->answers($this->response(['ventilationSystems' => [$this->system('D5c', true), $this->system('A1')]]));

        $this->assertSame($this->type(1), $answers['ventilation-type']);
        $this->assertSame(0, $answers['ventilation-heat-recovery']);
    }

    public function test_none_empties_the_answers(): void
    {
        $this->assertSame(
            ['ventilation-type' => null, 'ventilation-heat-recovery' => null],
            $this->answers($this->response([])),
        );
    }

    public function test_an_unknown_code_is_a_spec_violation(): void
    {
        $this->assertNotEmpty($this->withStatus($this->response(['ventilationSystems' => [$this->system('F1')]]), MappingStatus::SPEC_VIOLATION));
    }
}
