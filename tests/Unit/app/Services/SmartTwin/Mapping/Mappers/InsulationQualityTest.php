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
}
