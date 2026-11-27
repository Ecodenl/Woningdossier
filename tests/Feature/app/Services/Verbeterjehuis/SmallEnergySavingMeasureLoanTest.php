<?php

namespace Tests\Feature\app\Services\Verbeterjehuis;

use App\Enums\MappingType;
use App\Helpers\MyRegulationHelper;
use App\Models\Building;
use App\Models\CustomMeasureApplication;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\MeasureCategory;
use App\Models\Municipality;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\MappingService;
use App\Services\UserActionPlanAdviceService;
use App\Services\Verbeterjehuis\Payloads\Search;
use App\Services\Verbeterjehuis\RegulationService;
use App\Services\Verbeterjehuis\Verbeterjehuis;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

/**
 * HD-18: the small energy saving measures ("Kleine energiebesparende maatregelen") never get a loan.
 */
final class SmallEnergySavingMeasureLoanTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    private const int INSULATION = 1503;

    private Building $building;
    private InputSource $masterInputSource;

    protected function setUp(): void
    {
        parent::setUp();

        $this->masterInputSource = InputSource::master();
        $this->building = Building::factory()->create(['user_id' => User::factory()->create()->id]);

        $this->mapMeasureApplication('save-energy-with-light', [RegulationService::SMALL_ENERGY_SAVING_MEASURES]);
        $this->mapMeasureApplication('floor-insulation', [self::INSULATION]);
    }

    public function test_a_small_measure_gets_a_subsidy_but_no_loan(): void
    {
        $search = Search::init([
            $this->regulation('1', RegulationService::LOAN, [RegulationService::SMALL_ENERGY_SAVING_MEASURES]),
            $this->regulation('2', RegulationService::SUBSIDY, [RegulationService::SMALL_ENERGY_SAVING_MEASURES]),
        ])->forMeasure(MeasureApplication::findByShort('save-energy-with-light'));

        $this->assertTrue($search->getLoans()->isEmpty());
        $this->assertSame(['2'], $search->getSubsidies()->pluck('Id')->values()->all());
    }

    public function test_a_small_measure_gets_no_loan_from_a_loan_that_also_covers_other_measures(): void
    {
        $search = Search::init([
            $this->regulation(
                '1',
                RegulationService::LOAN,
                [self::INSULATION, RegulationService::SMALL_ENERGY_SAVING_MEASURES]
            ),
        ])->forMeasure(MeasureApplication::findByShort('save-energy-with-light'));

        $this->assertTrue($search->getLoans()->isEmpty());
    }

    public function test_another_measure_still_gets_a_loan(): void
    {
        $search = Search::init([
            $this->regulation(
                '1',
                RegulationService::LOAN,
                [self::INSULATION, RegulationService::SMALL_ENERGY_SAVING_MEASURES]
            ),
        ])->forMeasure(MeasureApplication::findByShort('floor-insulation'));

        $this->assertSame(['1'], $search->getLoans()->pluck('Id')->values()->all());
    }

    public function test_a_measure_mapped_to_more_measures_only_gets_a_loan_through_the_other_measures(): void
    {
        $this->mapMeasureApplication(
            'floor-insulation',
            [self::INSULATION, RegulationService::SMALL_ENERGY_SAVING_MEASURES]
        );
        $measureApplication = MeasureApplication::findByShort('floor-insulation');

        $smallOnly = Search::init([
            $this->regulation('1', RegulationService::LOAN, [RegulationService::SMALL_ENERGY_SAVING_MEASURES]),
        ])->forMeasure($measureApplication);
        $insulation = Search::init([
            $this->regulation('2', RegulationService::LOAN, [self::INSULATION]),
        ])->forMeasure($measureApplication);

        $this->assertTrue($smallOnly->getLoans()->isEmpty());
        $this->assertSame(['2'], $insulation->getLoans()->pluck('Id')->values()->all());
        $this->assertFalse(Search::isSmallEnergySavingMeasure($measureApplication));
    }

    public function test_loans_are_not_filtered_without_a_measure(): void
    {
        $search = Search::init([
            $this->regulation('1', RegulationService::LOAN, [RegulationService::SMALL_ENERGY_SAVING_MEASURES]),
        ]);

        $this->assertSame(['1'], $search->getLoans()->pluck('Id')->values()->all());
    }

    public function test_small_energy_saving_measures_are_recognised(): void
    {
        $this->assertTrue(Search::isSmallEnergySavingMeasure(MeasureApplication::findByShort('save-energy-with-light')));
        $this->assertFalse(Search::isSmallEnergySavingMeasure(MeasureApplication::findByShort('floor-insulation')));

        // Not mapped at all, so it gets no regulations anyway.
        $this->assertFalse(Search::isSmallEnergySavingMeasure(MeasureApplication::findByShort('crack-sealing')));

        $this->assertTrue(Search::isSmallEnergySavingMeasure(
            $this->customMeasureApplication(RegulationService::SMALL_ENERGY_SAVING_MEASURES)
        ));
        $this->assertFalse(Search::isSmallEnergySavingMeasure($this->customMeasureApplication(self::INSULATION)));
    }

    public function test_refreshing_sets_no_loan_for_a_small_measure(): void
    {
        $this->fakeVerbeterjehuis([
            $this->regulation('1', RegulationService::LOAN, [RegulationService::SMALL_ENERGY_SAVING_MEASURES]),
            $this->regulation('2', RegulationService::SUBSIDY, [RegulationService::SMALL_ENERGY_SAVING_MEASURES]),
            $this->regulation('3', RegulationService::LOAN, [self::INSULATION]),
        ]);

        $small = $this->advice(MeasureApplication::findByShort('save-energy-with-light'));
        $insulation = $this->advice(MeasureApplication::findByShort('floor-insulation'));
        $custom = $this->advice($this->customMeasureApplication(RegulationService::SMALL_ENERGY_SAVING_MEASURES));

        $service = UserActionPlanAdviceService::init()->forUser($this->building->user);
        foreach ([$small, $insulation, $custom] as $advice) {
            $service->refreshRegulations($advice);
        }

        $this->assertFalse($small->refresh()->loan_available);
        $this->assertTrue($small->subsidy_available);
        $this->assertFalse($custom->refresh()->loan_available);
        $this->assertTrue($custom->subsidy_available);
        $this->assertTrue($insulation->refresh()->loan_available);
    }

    public function test_the_overview_lists_no_loan_for_a_small_measure_with_an_old_loan_flag(): void
    {
        $this->fakeVerbeterjehuis([
            $this->regulation(
                '1',
                RegulationService::LOAN,
                [self::INSULATION, RegulationService::SMALL_ENERGY_SAVING_MEASURES]
            ),
            $this->regulation('2', RegulationService::SUBSIDY, [RegulationService::SMALL_ENERGY_SAVING_MEASURES]),
        ]);

        $small = MeasureApplication::findByShort('save-energy-with-light');
        $insulation = MeasureApplication::findByShort('floor-insulation');
        // Flags as stored before HD-18.
        $this->advice($small)->forceFill(['loan_available' => true, 'subsidy_available' => true])->saveQuietly();
        $this->advice($insulation)->forceFill(['loan_available' => true])->saveQuietly();

        $regulations = MyRegulationHelper::getRelevantRegulations($this->building, $this->masterInputSource);

        $this->assertSame([$insulation->measure_name], $regulations[RegulationService::LOAN][0]['advisable_names']);
        $this->assertSame([$small->measure_name], $regulations[RegulationService::SUBSIDY][0]['advisable_names']);
    }

    private function mapMeasureApplication(string $short, array $values): void
    {
        MappingService::init()
            ->from(MeasureApplication::findByShort($short))
            ->sync(array_map(fn (int $value) => $this->measure($value), $values));
    }

    private function customMeasureApplication(int $value): CustomMeasureApplication
    {
        $measureCategory = MeasureCategory::factory()->create();
        MappingService::init()
            ->from($measureCategory)
            ->sync([$this->measure($value)], MappingType::MEASURE_CATEGORY_VBJEHUIS->value);

        $customMeasureApplication = CustomMeasureApplication::factory()->create([
            'building_id' => $this->building->id,
            'input_source_id' => $this->masterInputSource->id,
        ]);
        MappingService::init()
            ->from($customMeasureApplication)
            ->sync([$measureCategory], MappingType::CUSTOM_MEASURE_APPLICATION_MEASURE_CATEGORY->value);

        return $customMeasureApplication;
    }

    private function advice($advisable): UserActionPlanAdvice
    {
        return UserActionPlanAdvice::withoutEvents(fn () => UserActionPlanAdvice::factory()->create([
            'user_id' => $this->building->user->id,
            'input_source_id' => $this->masterInputSource->id,
            'user_action_plan_advisable_type' => get_class($advisable),
            'user_action_plan_advisable_id' => $advisable->id,
            'category' => UserActionPlanAdviceService::CATEGORY_TO_DO,
            'visible' => true,
            'loan_available' => false,
            'subsidy_available' => false,
        ]));
    }

    private function fakeVerbeterjehuis(array $regulations): void
    {
        $municipality = Municipality::factory()->create();
        MappingService::init()
            ->from($municipality)
            ->sync([['Id' => 1, 'Name' => 'Test']], MappingType::MUNICIPALITY_VBJEHUIS->value);
        $this->building->update(['municipality_id' => $municipality->id]);

        $this->mock(Verbeterjehuis::class, function (MockInterface $mock) use ($regulations) {
            $mock->shouldReceive('regulation->search')->andReturn($regulations);
        });
    }

    private function measure(int $value): array
    {
        return ['Value' => $value, 'Label' => "Measure {$value}"];
    }

    private function regulation(string $id, string $type, array $values): array
    {
        return [
            'Id' => $id,
            'Title' => "Regulation {$id}",
            'Type' => $type,
            'TargetGroup' => 'Owner, Tenant',
            'Tags' => array_map(fn (int $value) => $this->measure($value), $values),
        ];
    }
}
