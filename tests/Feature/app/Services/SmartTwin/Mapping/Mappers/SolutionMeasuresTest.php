<?php

namespace Tests\Feature\app\Services\SmartTwin\Mapping\Mappers;

use App\Enums\MappingType;
use App\Models\MeasureApplication;
use App\Services\MappingService;
use App\Services\SmartTwin\Mapping\Mappers\SolutionMeasures;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Which measure a SmartTwin solution is, out of the mappings table.
 *
 * The one translation in this mapping that is not in code: SmartTwin's catalogue grows without
 * their API changing, so a new product has to be couplable without a deploy.
 */
final class SolutionMeasuresTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private const SOLUTION_ID = 'Insulate Facade Cavity|SmartTwin:Cavity_Insulation_EPS_Pearls';

    private SolutionMeasures $measures;

    protected function setUp(): void
    {
        parent::setUp();

        $this->measures = app(SolutionMeasures::class);
    }

    private function couple(string $solutionId, string $measureShort): void
    {
        MappingService::init()
            ->from($solutionId)
            ->sync(
                [MeasureApplication::findByShort($measureShort)],
                MappingType::SMARTTWIN_SOLUTION_MEASURE_APPLICATION->value,
            );
    }

    public function test_a_coupled_solution_resolves_to_its_measure(): void
    {
        $this->couple(self::SOLUTION_ID, 'cavity-wall-insulation');

        $this->assertSame('cavity-wall-insulation', $this->measures->shortFor(self::SOLUTION_ID));
    }

    public function test_an_uncoupled_solution_resolves_to_nothing(): void
    {
        $this->assertNull($this->measures->shortFor('Install Heat Pump|SmartTwin:Nog_Niet_Gekoppeld'));
    }

    public function test_the_id_has_to_match_exactly(): void
    {
        // The id carries a product variant, and two variants of the same measure are different
        // couplings. A near miss is a miss.
        $this->couple(self::SOLUTION_ID, 'cavity-wall-insulation');

        $this->assertNull($this->measures->shortFor('Insulate Facade Cavity|SmartTwin:Cavity_Insulation_Wool'));
    }

    public function test_couplings_of_another_kind_are_left_out_of_it(): void
    {
        // The mappings table also holds municipality and measure category couplings; a solution id
        // may only ever resolve through its own type.
        MappingService::init()
            ->from(self::SOLUTION_ID)
            ->sync(
                [MeasureApplication::findByShort('cavity-wall-insulation')],
                MappingType::MEASURE_APPLICATION_MEASURE_CATEGORY->value,
            );

        $this->assertNull($this->measures->shortFor(self::SOLUTION_ID));
    }

    public function test_a_coupling_can_be_pointed_at_another_measure(): void
    {
        // What changing a coupling in the admin comes down to.
        $this->couple(self::SOLUTION_ID, 'cavity-wall-insulation');
        $this->couple(self::SOLUTION_ID, 'facade-wall-insulation');

        $this->assertSame('facade-wall-insulation', $this->measures->shortFor(self::SOLUTION_ID));
    }

    public function test_a_coupling_is_for_one_product_only(): void
    {
        // One solution, one measure. Grouping products by the kind in their id is something the
        // coupling screen offers; making the lookup read that format would tie it to a grammar
        // SmartTwin has never documented.
        $this->couple(self::SOLUTION_ID, 'cavity-wall-insulation');

        $this->assertNull($this->measures->shortFor('Insulate Facade Cavity'));
        $this->assertNull($this->measures->shortFor('Insulate Facade Cavity|SmartTwin:Cavity_Insulation_Mineral_Wool'));
    }

    public function test_several_products_can_lead_to_the_same_measure(): void
    {
        // Which is how a kind gets covered: a row per product, all pointing at one measure.
        $this->couple(self::SOLUTION_ID, 'cavity-wall-insulation');
        $this->couple('Insulate Facade Cavity|SmartTwin:Cavity_Insulation_Mineral_Wool', 'cavity-wall-insulation');

        $this->assertSame('cavity-wall-insulation', $this->measures->shortFor(self::SOLUTION_ID));
        $this->assertSame(
            'cavity-wall-insulation',
            $this->measures->shortFor('Insulate Facade Cavity|SmartTwin:Cavity_Insulation_Mineral_Wool'),
        );
    }

    public function test_an_empty_id_resolves_to_nothing(): void
    {
        $this->assertNull($this->measures->shortFor(''));
    }
}
