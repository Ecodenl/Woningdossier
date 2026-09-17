<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Services\SmartTwin\Mapping\Mappers\FacadeParts;
use PHPUnit\Framework\TestCase;

final class FacadePartsTest extends TestCase
{
    /**
     * @param  array<int, array<int, array<string, mixed>>>  $assemblies  Facade parts per assembly.
     * @return array<string, mixed>
     */
    private function response(array $assemblies, string $section = 'current'): array
    {
        return [
            $section => [
                'properties' => [
                    'facadeAssemblies' => array_map(
                        fn (array $parts) => ['area' => 0, 'facades' => $parts, 'windows' => [], 'doors' => [], 'panels' => []],
                        $assemblies,
                    ),
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function part(float $area, float $rcValue = 0.35, ?int $facadeType = 1): array
    {
        return array_filter([
            'area'       => $area,
            'rcValue'    => $rcValue,
            'facadeType' => $facadeType,
        ], fn ($value) => ! is_null($value));
    }

    private function parts(array $assemblies, string $section = 'current'): FacadeParts
    {
        return FacadeParts::fromResponse($this->response($assemblies, $section), $section);
    }

    public function test_it_gathers_the_parts_of_every_assembly(): void
    {
        $parts = $this->parts([
            [$this->part(13.14), $this->part(11.75)],
            [$this->part(32.52)],
            [$this->part(48.28)],
        ]);

        $this->assertSame(4, $parts->count());
        $this->assertEqualsWithDelta(105.69, $parts->totalArea(), 0.001);
    }

    public function test_an_assembly_without_closed_parts_contributes_nothing(): void
    {
        // A wall that is all window and door. It still exists as an assembly, which is why the
        // first part of the response is not necessarily the first part of the first assembly.
        $parts = $this->parts([[], [$this->part(26.22)], []]);

        $this->assertSame(1, $parts->count());
        $this->assertEqualsWithDelta(26.22, $parts->totalArea(), 0.001);
    }

    public function test_a_response_without_facades_is_empty_rather_than_broken(): void
    {
        $this->assertSame(0, FacadeParts::fromResponse([], 'current')->count());
        $this->assertSame(0.0, FacadeParts::fromResponse([], 'current')->totalArea());
        $this->assertNull(FacadeParts::fromResponse([], 'current')->dominantType());
    }

    public function test_the_rc_value_is_weighted_by_surface(): void
    {
        // A small well insulated part next to a large bare one should read as bare, which a flat
        // average would not give: that would be 2.175 instead of 0.85.
        $parts = $this->parts([[$this->part(10.0, 4.0), $this->part(90.0, 0.35)]]);

        $this->assertEqualsWithDelta(0.715, $parts->weightedRcValue(), 0.001);
    }

    public function test_a_uniform_facade_reads_as_its_own_rc_value(): void
    {
        $parts = $this->parts([[$this->part(30.0, 1.63)], [$this->part(70.0, 1.63)]]);

        $this->assertEqualsWithDelta(1.63, $parts->weightedRcValue(), 0.001);
    }

    public function test_there_is_no_rc_value_without_surface_to_weigh_with(): void
    {
        $this->assertNull($this->parts([[$this->part(0.0, 2.5)]])->weightedRcValue());
        $this->assertNull($this->parts([])->weightedRcValue());
    }

    public function test_only_the_parts_whose_rc_goes_up_count_as_insulated(): void
    {
        $current = $this->parts([[$this->part(30.0, 0.35), $this->part(20.0, 0.35)]]);
        $scenario = $this->parts([[$this->part(30.0, 1.63), $this->part(20.0, 0.35)]], 'scenario');

        $this->assertEqualsWithDelta(30.0, $scenario->areaImprovedOver($current), 0.001);
    }

    public function test_an_advice_that_leaves_the_facade_alone_insulates_nothing(): void
    {
        $current = $this->parts([[$this->part(30.0, 0.35)]]);
        $scenario = $this->parts([[$this->part(30.0, 0.35)]], 'scenario');

        $this->assertSame(0.0, $scenario->areaImprovedOver($current));
    }

    public function test_it_refuses_to_pair_up_sections_of_a_different_length(): void
    {
        // Parts are matched by position, so a different count means the pairing would be a guess —
        // and this number is what the measure's cost and savings are calculated over.
        $current = $this->parts([[$this->part(30.0, 0.35)]]);
        $scenario = $this->parts([[$this->part(30.0, 1.63), $this->part(20.0, 1.63)]], 'scenario');

        $this->assertNull($scenario->areaImprovedOver($current));
    }

    public function test_the_type_with_the_most_surface_wins(): void
    {
        $parts = $this->parts([[
            $this->part(10.0, facadeType: 1),
            $this->part(40.0, facadeType: 0),
        ]]);

        $this->assertSame(0, $parts->dominantType());
    }

    public function test_a_tie_resolves_to_the_lowest_type_so_the_answer_is_stable(): void
    {
        $parts = $this->parts([[
            $this->part(25.0, facadeType: 2),
            $this->part(25.0, facadeType: 1),
        ]]);

        $this->assertSame(1, $parts->dominantType());
    }

    public function test_parts_without_a_type_are_left_out_of_the_tally(): void
    {
        $parts = $this->parts([[
            $this->part(80.0, facadeType: null),
            $this->part(10.0, facadeType: 1),
        ]]);

        $this->assertSame(1, $parts->dominantType());
    }
}
