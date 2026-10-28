<?php

namespace Tests\Feature\app\Services\SmartTwin\Mapping\Elements;

use App\Services\SmartTwin\Mapping\Elements\AdvisedElements;
use App\Services\SmartTwin\Mapping\Elements\Element;
use App\Services\SmartTwin\Mapping\Elements\RoofShape;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\app\Services\SmartTwin\CouplesSolutions;
use Tests\TestCase;

/**
 * Which parts of the dwelling the solutions of a response work on, through their coupled measure.
 * The solution ids are the three of the first example response.
 */
final class AdvisedElementsTest extends TestCase
{
    use RefreshDatabase;
    use CouplesSolutions;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private const FLOOR = 'Insulate Ground Floor|SmartTwin:Ground_Floor_Insulation_PUR_100';
    private const FLAT_ROOF = 'Insulate Flat Roof Outside|SmartTwin:Flat_Roof_Exterior_Insulation_PIR_100';
    private const PITCHED_ROOF = 'Insulate Pitched Roof Inside|SmartTwin:Pitched_Roof_Interior_Insulation_PIR_120';

    private function response(string ...$solutionIds): array
    {
        return ['scenario' => ['solutions' => array_map(fn (string $id) => ['id' => $id], $solutionIds)]];
    }

    public function test_a_coupled_solution_covers_the_element_of_its_measures_step(): void
    {
        $this->couple(self::FLOOR, 'floor-insulation');

        $advised = app(AdvisedElements::class);
        $response = $this->response(self::FLOOR);

        $this->assertTrue($advised->covers($response, Element::FLOOR));
        $this->assertTrue($advised->covers($response, Element::CRAWLSPACE));
        $this->assertFalse($advised->covers($response, Element::FACADE));
    }

    public function test_a_roof_solution_covers_only_its_own_shape(): void
    {
        $this->couple(self::FLAT_ROOF, 'roof-insulation-flat-current');

        $advised = app(AdvisedElements::class);
        $response = $this->response(self::FLAT_ROOF);

        $this->assertTrue($advised->covers($response, Element::ROOF, RoofShape::FLAT));
        $this->assertFalse($advised->covers($response, Element::ROOF, RoofShape::PITCHED));
        $this->assertTrue($advised->covers($response, Element::ROOF));
    }

    public function test_an_uncoupled_solution_covers_nothing(): void
    {
        $advised = app(AdvisedElements::class);

        $this->assertSame([], $advised->scopes($this->response(self::PITCHED_ROOF)));
    }
}
