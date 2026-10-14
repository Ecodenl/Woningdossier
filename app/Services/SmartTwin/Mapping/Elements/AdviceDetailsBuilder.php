<?php

namespace App\Services\SmartTwin\Mapping\Elements;

use App\Services\SmartTwin\Mapping\Mappers\FacadeParts;
use App\Services\SmartTwin\Mapping\Mappers\FloorParts;
use App\Services\SmartTwin\Mapping\Mappers\InsulatedParts;
use App\Services\SmartTwin\Mapping\Mappers\RoofParts;
use App\Services\SmartTwin\SmartTwinAdviceDetails;

/**
 * The details an advice carries: its solution, and every element its measure works on as current
 * and scenario describe it.
 *
 * Which elements follows the measure (ElementScope), not the scenario: a floor solution covers the
 * whole floor even when the scenario forgets to insulate it, as the first example response did. What
 * the scenario changes shows in `after` and in the insulated surface.
 */
class AdviceDetailsBuilder
{
    public function __construct(private readonly ElementReader $reader)
    {
    }

    /**
     * @param  array<string, mixed>  $response
     * @param  array<string, mixed>  $solution  The `scenario.solutions.*` entry the advice comes from.
     */
    public function for(array $response, array $solution, string $measureShort, ?string $stepShort): SmartTwinAdviceDetails
    {
        $elements = [];

        foreach (ElementScope::forMeasure($measureShort, $stepShort) as $scope) {
            $before = $this->reader->read($scope->element, $response, 'current', $scope->shape);
            $after = $this->reader->read($scope->element, $response, 'scenario', $scope->shape);

            $entry = array_filter([
                'shape'    => $scope->shape?->value,
                'part_ids' => $before->partIds,
            ], fn ($value) => ! is_null($value));

            $insulated = $this->insulatedArea($scope, $response);
            if (! is_null($insulated)) {
                $entry['insulated_area'] = $insulated;
            }

            $entry['before'] = $before->toArray();
            $entry['after'] = $after->toArray() === $before->toArray() ? null : $after->toArray();

            $elements[$scope->element->value] = $entry;
        }

        return new SmartTwinAdviceDetails([SmartTwinAdviceDetails::solution($solution)], $elements);
    }

    /**
     * The surface the scenario insulates, for the elements that are insulated by surface; the same
     * sum as the te-isoleren answers.
     *
     * @param  array<string, mixed>  $response
     */
    private function insulatedArea(ElementScope $scope, array $response): ?float
    {
        $parts = fn (string $section): ?InsulatedParts => match ($scope->element) {
            Element::FACADE => FacadeParts::fromResponse($response, $section),
            Element::FLOOR  => FloorParts::fromResponse($response, $section),
            Element::ROOF   => is_null($scope->shape)
                ? RoofParts::fromResponse($response, $section)
                : RoofParts::fromResponse($response, $section)->ofShape($scope->shape),
            default         => null,
        };

        $current = $parts('current');

        return is_null($current) ? null : round($parts('scenario')->areaImprovedOver($current), 2);
    }
}
