<?php

namespace Tests\Unit\app\Services\SmartTwin\Mapping\Mappers;

use App\Services\SmartTwin\Mapping\Mappers\CrawlspaceHeight;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Both sides of every boundary, because that is where a band table goes wrong.
 */
final class CrawlspaceHeightTest extends TestCase
{
    /** @return array<string, array{null|float, int}> */
    public static function heights(): array
    {
        return [
            'vlak onder maaiveld'     => [-0.05, CrawlspaceHeight::VERY_LOW],
            'net onder laag'          => [-0.29, CrawlspaceHeight::VERY_LOW],
            'ondergrens laag'         => [-0.30, CrawlspaceHeight::LOW],
            'midden in laag'          => [-0.45, CrawlspaceHeight::LOW],
            'net onder best hoog'     => [-0.49, CrawlspaceHeight::LOW],
            'ondergrens best hoog'    => [-0.50, CrawlspaceHeight::HIGH],
            'diepe kruipruimte'       => [-1.20, CrawlspaceHeight::HIGH],
            'geen hoogte opgegeven'   => [null, CrawlspaceHeight::UNKNOWN],
        ];
    }

    #[DataProvider('heights')]
    public function test_it_places_a_height_in_its_band(?float $height, int $expected): void
    {
        $this->assertSame($expected, CrawlspaceHeight::orderFor($height));
    }

    public function test_the_sign_carries_no_meaning(): void
    {
        // SmartTwin measures relative to ground level and goes negative below it, which is the usual
        // case. Hoomdossier only asks how much room there is.
        foreach ([-0.45, 0.45] as $height) {
            $this->assertSame(CrawlspaceHeight::LOW, CrawlspaceHeight::orderFor($height));
        }

        foreach ([-0.80, 0.80] as $height) {
            $this->assertSame(CrawlspaceHeight::HIGH, CrawlspaceHeight::orderFor($height));
        }
    }

    public function test_a_crawlspace_at_ground_level_is_the_lowest_band_rather_than_unknown(): void
    {
        // Zero is a height, not a missing one.
        $this->assertSame(CrawlspaceHeight::VERY_LOW, CrawlspaceHeight::orderFor(0.0));
    }

    public function test_every_band_resolves_to_a_different_option(): void
    {
        // The whole reason this returns an order: two of the four share calculate value 0, so the
        // orders are what keeps them apart.
        $orders = [CrawlspaceHeight::HIGH, CrawlspaceHeight::LOW, CrawlspaceHeight::VERY_LOW, CrawlspaceHeight::UNKNOWN];

        $this->assertSame($orders, array_values(array_unique($orders)));
    }

    /** @return array<string, array{null|float, string}> */
    public static function descriptions(): array
    {
        return [
            'uit het rapport'   => [-0.50, 'hoogte 0,50 m t.o.v. maaiveld valt vanaf 0,50 m'],
            'midden in laag'    => [-0.45, 'hoogte 0,45 m t.o.v. maaiveld valt tussen 0,30 m en 0,50 m'],
            'heel laag'         => [-0.20, 'hoogte 0,20 m t.o.v. maaiveld valt onder 0,30 m'],
            'geen hoogte'       => [null, 'geen hoogte opgegeven'],
        ];
    }

    #[DataProvider('descriptions')]
    public function test_it_describes_a_height_in_the_terms_of_the_table(?float $height, string $expected): void
    {
        $this->assertSame($expected, CrawlspaceHeight::describe($height));
    }

    public function test_the_description_never_contradicts_the_band(): void
    {
        $bands = [
            CrawlspaceHeight::HIGH     => 'vanaf 0,50 m',
            CrawlspaceHeight::LOW      => 'tussen 0,30 m en 0,50 m',
            CrawlspaceHeight::VERY_LOW => 'onder 0,30 m',
        ];

        for ($centimetres = 0; $centimetres <= 150; ++$centimetres) {
            $height = -$centimetres / 100;

            $this->assertStringEndsWith(
                $bands[CrawlspaceHeight::orderFor($height)],
                CrawlspaceHeight::describe($height),
                "hoogte {$height}",
            );
        }
    }
}
