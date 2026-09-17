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
            'op de grens laag/hoog'   => [-0.50, CrawlspaceHeight::LOW],
            'net boven de grens'      => [-0.51, CrawlspaceHeight::HIGH],
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
}
