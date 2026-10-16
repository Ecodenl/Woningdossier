<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Elements;

use App\Services\SmartTwin\Mapping\Elements\Element;
use App\Services\SmartTwin\Mapping\Elements\ElementScope;
use App\Services\SmartTwin\Mapping\Elements\RoofShape;
use PHPUnit\Framework\TestCase;

/**
 * The element of the externally advised measures. The feature test next to the seeded measures
 * checks every measure has one; this checks the ones whose short decides it.
 */
final class ElementScopeTest extends TestCase
{
    public function test_the_flat_roof_insulated_from_inside_works_on_the_flat_roof_only(): void
    {
        $scopes = ElementScope::forMeasure('roof-insulation-flat-inside', 'roof-insulation');

        $this->assertCount(1, $scopes);
        $this->assertSame(Element::ROOF, $scopes[0]->element);
        $this->assertSame(RoofShape::FLAT, $scopes[0]->shape);
    }

    public function test_sun_shading_and_the_induction_hob_work_on_no_element_whatever_their_step(): void
    {
        $this->assertSame([], ElementScope::forMeasure('sun-shading', 'insulated-glazing'));
        $this->assertSame([], ElementScope::forMeasure('induction-hob', 'heating'));
    }

    public function test_another_measure_on_those_steps_keeps_its_element(): void
    {
        $this->assertSame(Element::WINDOWS, ElementScope::forMeasure('vacuum-glass-only', 'insulated-glazing')[0]->element);
        $this->assertSame(
            [Element::HEATING, Element::HOT_WATER],
            array_map(fn (ElementScope $scope) => $scope->element, ElementScope::forMeasure('district-heating-connect', 'heating')),
        );
    }
}
