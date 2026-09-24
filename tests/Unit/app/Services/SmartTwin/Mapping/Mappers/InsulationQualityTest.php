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

    /** @return array<string, array{string, float, string}> */
    public static function descriptions(): array
    {
        return [
            // The two cases the stuurgroep asked about.
            'gevel uit het rapport'           => ['wall', 0.69, 'Rc 0,69 valt onder 0,80'],
            'vloer uit het rapport'           => ['floor', 0.65, 'Rc 0,65 valt tussen 0,20 en 1,00'],

            'gevel op een grens'              => ['wall', 0.80, 'Rc 0,80 valt tussen 0,80 en 2,08'],
            'gevel in de hoogste band'        => ['wall', 5.10, 'Rc 5,10 valt vanaf 4,46'],
            'vloer onder de laagste grens'    => ['floor', 0.15, 'Rc 0,15 valt onder 0,20'],
            'vloer in de hoogste band'        => ['floor', 4.35, 'Rc 4,35 valt vanaf 4,35'],

            // Rounding would print "0,80 valt onder 0,80"; truncating keeps the printed value on
            // the same side of the boundary as the real one.
            'gewogen net onder een grens'     => ['wall', 0.7996, 'Rc 0,79 valt onder 0,80'],
            // 0.29 * 100 is 28.999999999999996 in floating point.
            'geen afrondingsruis'             => ['floor', 0.29, 'Rc 0,29 valt tussen 0,20 en 1,00'],
        ];
    }

    #[DataProvider('descriptions')]
    public function test_it_describes_a_value_in_the_terms_of_the_table(string $element, float $rcValue, string $expected): void
    {
        $described = 'wall' === $element
            ? InsulationQuality::describeWall($rcValue)
            : InsulationQuality::describeFloor($rcValue);

        $this->assertSame($expected, $described);
    }

    public function test_the_description_never_contradicts_the_classification(): void
    {
        // The band in the note and the level that is written come from the same table, walked by two
        // different methods. Every hundredth from 0 to 6 has to land in the same place in both.
        $floorNames = [2 => 'onder 0,20', 3 => 'tussen 0,20 en 1,00', 4 => 'tussen 1,00 en 1,75',
                       5 => 'tussen 1,75 en 3,00', 6 => 'tussen 3,00 en 4,35', 7 => 'vanaf 4,35'];

        for ($hundredths = 0; $hundredths <= 600; ++$hundredths) {
            $rcValue = $hundredths / 100;

            $this->assertStringEndsWith(
                $floorNames[InsulationQuality::forFloor($rcValue)],
                InsulationQuality::describeFloor($rcValue),
                "Rc {$rcValue}",
            );
        }
    }
}
