<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Services\SmartTwin\Mapping\Mappers\InsulationQuality;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The boundaries of the facade classification, including both sides of each one — a table like this
 * goes wrong at its edges or not at all.
 */
final class InsulationQualityTest extends TestCase
{
    /** @return array<string, array{float, int}> */
    public static function walls(): array
    {
        return [
            'onbewerkte spouwmuur'      => [0.35, 2],
            'net onder matig'           => [0.79, 2],
            'ondergrens matig'          => [0.80, 3],
            'net onder redelijk'        => [2.07, 3],
            'ondergrens redelijk'       => [2.08, 4],
            'net onder goed'            => [3.18, 4],
            'ondergrens goed'           => [3.19, 5],
            'net onder zeer goed'       => [4.45, 5],
            'ondergrens zeer goed'      => [4.46, 6],
            'ruim boven zeer goed'      => [8.00, 6],
        ];
    }

    #[DataProvider('walls')]
    public function test_it_places_a_facade_rc_value_in_its_level(float $rcValue, int $expected): void
    {
        $this->assertSame($expected, InsulationQuality::forWall($rcValue));
    }

    public function test_a_facade_without_insulation_never_reads_as_insulated(): void
    {
        $this->assertSame(2, InsulationQuality::forWall(0.0));
    }

    /** @return array<string, array{float, int}> */
    public static function floors(): array
    {
        return [
            'onbewerkte vloer'      => [0.15, 2],
            'net onder slecht'      => [0.19, 2],
            'ondergrens slecht'     => [0.20, 3],
            'net onder matig'       => [0.99, 3],
            'ondergrens matig'      => [1.00, 4],
            'net onder redelijk'    => [1.74, 4],
            'ondergrens redelijk'   => [1.75, 5],
            'net onder goed'        => [2.99, 5],
            'ondergrens goed'       => [3.00, 6],
            'net onder zeer goed'   => [4.34, 6],
            'ondergrens zeer goed'  => [4.35, 7],
        ];
    }

    #[DataProvider('floors')]
    public function test_it_places_a_floor_rc_value_in_its_level(float $rcValue, int $expected): void
    {
        $this->assertSame($expected, InsulationQuality::forFloor($rcValue));
    }

    public function test_the_two_elements_run_on_their_own_tables(): void
    {
        // Rc 0,5 is nothing at all on a facade and Slechte isolatie on a floor, and the floor scale
        // sits a level higher from Matige isolatie up.
        $this->assertSame(2, InsulationQuality::forWall(0.5));
        $this->assertSame(3, InsulationQuality::forFloor(0.5));
    }
}
