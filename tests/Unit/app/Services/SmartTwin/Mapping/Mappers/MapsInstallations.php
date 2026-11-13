<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Enums\SmartTwin\MappingStatus;
use App\Models\Building;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\MappingResult;
use App\Services\SmartTwin\Mapping\ResponseFlattener;

/**
 * Runs an installation mapper over a response as the import does: every claimed leaf, once.
 */
trait MapsInstallations
{
    abstract protected function mapper(): FieldMapper;

    /**
     * A response with the given systems, now and after the advice.
     *
     * @param  array<string, array<int, array<string, mixed>>>  $current   systems by key, e.g. `heatingSystems`
     * @param  null|array<string, array<int, array<string, mixed>>>  $scenario
     * @return array<string, mixed>
     */
    private function response(array $current, ?array $scenario = null): array
    {
        $section = fn (array $systems) => ['properties' => array_merge([
            'heatingSystems' => [], 'hotWaterSystems' => [], 'solarSystems' => [], 'ventilationSystems' => [],
            'pvSystems' => [], 'coolingSystems' => [],
        ], $systems)];

        return ['current' => $section($current), 'scenario' => $section($scenario ?? $current)];
    }

    /** @return array<int, MappingResult> */
    private function mapClaimed(array $response): array
    {
        $claimed = array_flip($this->mapper()->paths());
        $results = [];

        foreach ((new ResponseFlattener())->flatten($response) as $leaf) {
            if (isset($claimed[$leaf->pathGroup])) {
                $result = $this->mapper()->map(new Building(), $leaf, $response);
                array_push($results, ...(is_array($result) ? $result : [$result]));
            }
        }

        return $results;
    }

    /** @return array<string, mixed> target => value, each answered once */
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

    /** @return array<int, MappingResult> */
    private function withStatus(array $response, MappingStatus $status): array
    {
        return array_values(array_filter($this->mapClaimed($response), fn (MappingResult $result) => $status === $result->status));
    }
}
