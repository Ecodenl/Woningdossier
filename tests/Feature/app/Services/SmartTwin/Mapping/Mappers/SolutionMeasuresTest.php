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

    public function test_a_coupling_on_the_kind_covers_every_product_of_that_kind(): void
    {
        // What the sync command writes: one row for the kind of measure. Every EPS, wool and foam
        // variant of cavity insulation resolves through it, including ones added after the sync.
        $this->couple('Insulate Facade Cavity', 'cavity-wall-insulation');

        $this->assertSame('cavity-wall-insulation', $this->measures->shortFor(self::SOLUTION_ID));
        $this->assertSame(
            'cavity-wall-insulation',
            $this->measures->shortFor('Insulate Facade Cavity|SmartTwin:Cavity_Insulation_Mineral_Wool'),
        );
    }

    public function test_a_coupling_on_the_product_wins_from_the_one_on_its_kind(): void
    {
        // A product that turns out to be a different measure can be given its own row without
        // disturbing the rest of its kind.
        $this->couple('Insulate Facade Cavity', 'cavity-wall-insulation');
        $this->couple(self::SOLUTION_ID, 'facade-wall-insulation');

        $this->assertSame('facade-wall-insulation', $this->measures->shortFor(self::SOLUTION_ID));
        $this->assertSame(
            'cavity-wall-insulation',
            $this->measures->shortFor('Insulate Facade Cavity|SmartTwin:Cavity_Insulation_Mineral_Wool'),
        );
    }

    public function test_an_id_without_a_provider_has_nothing_to_fall_back_to(): void
    {
        // The older id format, as seen in an earlier sample: no provider, so no kind to read off.
        $this->assertNull($this->measures->kindOf('InsulateFacadeCavityInsulation'));
        $this->assertNull($this->measures->shortFor('InsulateFacadeCavityInsulation'));
    }

    public function test_it_reads_the_kind_off_an_id(): void
    {
        $this->assertSame('Insulate Facade Cavity', $this->measures->kindOf(self::SOLUTION_ID));
    }
}
