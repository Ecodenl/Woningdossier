<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Enums\SmartTwin\MappingStatus;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Mappers\CoolingMapper;
use PHPUnit\Framework\TestCase;

/**
 * No response seen so far has cooling, so this holds the mapper against the spec.
 */
final class CoolingMapperTest extends TestCase
{
    use MapsInstallations;

    private CoolingMapper $mapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mapper = new CoolingMapper(new FakeElementValues());
    }

    protected function mapper(): FieldMapper
    {
        return $this->mapper;
    }

    private function system(int $type): array
    {
        return ['energyCarrier' => 0, 'generatorType' => $type, 'heatPumpSource' => 0, 'description' => '', 'id' => uniqid()];
    }

    public function test_every_system_counts(): void
    {
        $answers = $this->answers($this->response(['coolingSystems' => [$this->system(1), $this->system(4), $this->system(1)]]));

        $this->assertSame(['cooling-type' => ['compression', 'free-cooling']], $answers);
    }

    public function test_none_is_geen_koeling(): void
    {
        $this->assertSame(['cooling-type' => ['none']], $this->answers($this->response([])));
    }

    public function test_an_unknown_type_is_a_spec_violation(): void
    {
        $this->assertNotEmpty($this->withStatus($this->response(['coolingSystems' => [$this->system(9)]]), MappingStatus::SPEC_VIOLATION));
    }
}
