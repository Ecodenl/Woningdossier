<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Enums\AdviceSource;
use App\Enums\SmartTwin\EventType;
use App\Enums\SmartTwin\MappingStatus;
use App\Models\Building;
use App\Models\BuildingRoofType;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\RoofType;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\ExecutedAdviceService;
use App\Services\SmartTwin\Mapping\Mappers\ElementValues;
use App\Services\SmartTwin\Mapping\MappingReport;
use App\Services\SmartTwin\SmartTwinService;
use App\Services\UserActionPlanAdviceService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * A whole import through the real mappers, on a response cut down from the first example dossier:
 * one facade, a pitched and a flat roof, a floor with a crawlspace, and solutions for the floor and
 * both roofs. The facade improves without a solution and the floor has a solution without
 * improving — the two disagreements that dossier really has.
 */
final class SmartTwinImportTest extends TestCase
{
    use RefreshDatabase;
    use CouplesSolutions;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private const FLOOR = 'Insulate Ground Floor|SmartTwin:Ground_Floor_Insulation_PUR_100';
    private const FLAT_ROOF = 'Insulate Flat Roof Outside|SmartTwin:Flat_Roof_Exterior_Insulation_PIR_100';
    private const PITCHED_ROOF = 'Insulate Pitched Roof Inside|SmartTwin:Pitched_Roof_Interior_Insulation_PIR_120';

    private Building $building;
    private InputSource $coach;
    private InputSource $master;

    protected function setUp(): void
    {
        parent::setUp();

        $this->building = User::first()->building;
        $this->coach = InputSource::findByShort(InputSource::COACH_SHORT);
        $this->master = InputSource::findByShort(InputSource::MASTER_SHORT);

        $this->couple(self::FLOOR, 'floor-insulation');
        $this->couple(self::FLAT_ROOF, 'roof-insulation-flat-current');
        $this->couple(self::PITCHED_ROOF, 'roof-insulation-pitched-inside');
    }

    private function roof(int $inclination, float $area, float $rcValue, string $id): array
    {
        return ['inclination' => $inclination, 'orientation' => 2, 'windows' => [], 'area' => $area, 'id' => "a-{$id}", 'description' => '',
                'roofs' => [['roofType' => 0, 'insulationInConstruction' => false, 'insulationInterior' => false,
                             'insulationExterior' => true, 'insulationThickness' => 0, 'rcValue' => $rcValue,
                             'area' => $area, 'id' => $id, 'description' => '']]];
    }

    private function section(float $facadeRc, float $pitchedRc, ?float $flatRc): array
    {
        $roofs = [$this->roof(47, 39.75, $pitchedRc, 'pitched')];

        if (! is_null($flatRc)) {
            $roofs[] = $this->roof(1, 30.73, $flatRc, 'flat');
        }

        return ['properties' => [
            'roofAssemblies'   => $roofs,
            'facadeAssemblies' => [[
                'inclination' => 90, 'orientation' => 6, 'windows' => [], 'doors' => [], 'panels' => [], 'area' => 57.21, 'id' => 'fa',
                'description' => '', 'facades' => [['cavityThickness' => null, 'facadeType' => 1, 'insulationThickness' => 0,
                    'insulationInConstruction' => true, 'insulationInterior' => false, 'insulationExterior' => false,
                    'rcValue' => $facadeRc, 'area' => 40.98, 'id' => 'facade', 'description' => '']],
            ]],
            'floorAssemblies' => [[
                'floors' => [['floorType' => 2, 'insulationInConstruction' => false, 'insulationInterior' => false,
                    'insulationExterior' => true, 'rcValue' => 0.52, 'insulationThickness' => 0, 'cavity' => false,
                    'thermoPillows' => 0, 'area' => 58.12, 'id' => 'floor', 'description' => '']],
                'crawlspaces' => [['floorInsulation' => 0, 'floorRbf' => 0, 'floorRbw' => 0, 'heightAboveGroundLevel' => -0.5,
                    'ventilation' => true, 'area' => 0, 'id' => 'crawlspace', 'description' => '']],
                'area' => 58.12, 'id' => 'fl', 'description' => '',
            ]],
        ]];
    }

    private function solution(string $id, float $cost): array
    {
        return ['id' => $id, 'name' => $id, 'details' => [['id' => 'thickness', 'displayName' => 'Dikte', 'value' => 100, 'unit' => 'mm']],
                'adviceSolutionCost' => ['totalCostWithTax' => $cost, 'additionalCosts' => [], 'exceptionalCosts' => [],
                    'primaryCosts' => [['code' => 'X', 'title' => 'X', 'description' => '', 'quantity' => 1, 'unit' => 'pst', 'costWithTax' => $cost]]]];
    }

    private function response(bool $withFlatRoof = true): array
    {
        $solutions = [$this->solution(self::FLOOR, 3613.26), $this->solution(self::PITCHED_ROOF, 3149.77)];
        if ($withFlatRoof) {
            $solutions[] = $this->solution(self::FLAT_ROOF, 8446.54);
        }

        $scenario = $this->section(2.58, 4.79, $withFlatRoof ? 4.22 : null);
        $scenario['solutions'] = $solutions;
        $scenario['totalCostWithTax'] = 15209.57;
        $scenario['energyCostSavingsPerMonth'] = 35;
        // As in the first response with a subsidy (dossier 100271): one scheme, many decimals.
        $scenario['totalSubsidy'] = 6853.749263548063;
        $scenario['subsidies'] = [['name' => 'ISDE', 'amount' => 6853.749263548063]];

        $current = $this->section(1.3, 1.3, $withFlatRoof ? 1.3 : null);
        $current['calculationResult'] = ['energyConsumptionGas' => 1623.6915, 'energyLabel' => 'C'];
        $scenario['calculationResult'] = ['energyConsumptionGas' => 1339.7759, 'energyLabel' => 'B'];

        return [
            'current'   => $current,
            'scenario'  => $scenario,
            'solutions' => [],
        ];
    }

    private function import(array $response): MappingReport
    {
        return app(SmartTwinService::class)->processResults($this->building, $response, EventType::COACH_SCAN_FINISHED);
    }

    private function roofRow(string $shape, InputSource $inputSource): ?BuildingRoofType
    {
        return BuildingRoofType::allInputSources()
            ->where('building_id', $this->building->id)
            ->where('input_source_id', $inputSource->id)
            ->where('roof_type_id', RoofType::findByShort($shape)->id)
            ->first();
    }

    private function answer(string $short, ?InputSource $inputSource = null): mixed
    {
        return $this->building->getAnswer($inputSource ?? $this->master, ToolQuestion::findByShort($short));
    }

    private function advices(): array
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('input_source_id', $this->master->id)
            ->where('user_action_plan_advisable_type', MeasureApplication::class)
            ->get()
            ->mapWithKeys(fn (UserActionPlanAdvice $a) => [MeasureApplication::find($a->user_action_plan_advisable_id)->short => $a])
            ->all();
    }

    public function test_the_roof_lands_per_shape_with_its_decimals(): void
    {
        $this->import($this->response());

        $this->assertEqualsWithDelta(39.75, (float) $this->roofRow('pitched', $this->master)?->roof_surface, 0.001);
        $this->assertEqualsWithDelta(30.73, (float) $this->roofRow('flat', $this->master)?->roof_surface, 0.001);
        $this->assertEqualsCanonicalizing(
            [RoofType::findByShort('pitched')->id, RoofType::findByShort('flat')->id],
            $this->answer('current-roof-types'),
        );
    }

    public function test_each_shape_keeps_its_own_insulation_level(): void
    {
        // Saving the level of the whole roof copies it onto every shape row, so the shapes have to be
        // written after it. Pitched at Rc 1,3 is Matig, flat at 0,2 is Geen, and the whole roof
        // weighs out at 0,82: Slecht.
        $response = $this->response();
        $response['current']['properties']['roofAssemblies'][1]['roofs'][0]['rcValue'] = 0.2;

        $this->import($response);

        $levels = app(ElementValues::class);
        $this->assertSame($levels->idFor('roof-insulation', 4), $this->roofRow('pitched', $this->master)->element_value_id);
        $this->assertSame($levels->idFor('roof-insulation', 2), $this->roofRow('flat', $this->master)->element_value_id);
        $this->assertSame($levels->idFor('roof-insulation', 3), (int) $this->answer('current-roof-insulation'));
    }

    public function test_a_second_scan_without_the_flat_roof_removes_it_here_and_in_master(): void
    {
        $this->import($this->response());
        $this->import($this->response(withFlatRoof: false));

        $this->assertNull($this->roofRow('flat', $this->coach));
        $this->assertNull($this->roofRow('flat', $this->master));
        $this->assertNotNull($this->roofRow('pitched', $this->master));
    }

    public function test_the_package_replaces_the_action_plan_and_starts_in_to_do(): void
    {
        $this->import($this->response());

        $advices = $this->advices();

        $this->assertEqualsCanonicalizing(
            ['floor-insulation', 'roof-insulation-pitched-inside', 'roof-insulation-flat-current'],
            array_keys($advices),
        );

        foreach ($advices as $short => $advice) {
            $this->assertSame(AdviceSource::SMART_TWIN, $advice->source, $short);
            // Rc 1,3 counts as insulated on the roof scale; the package still starts in to-do.
            $this->assertSame(UserActionPlanAdviceService::CATEGORY_TO_DO, $advice->category, $short);
        }
    }

    public function test_each_package_card_carries_its_smarttwin_details_into_master(): void
    {
        $this->import($this->response());

        $floor = $this->advices()['floor-insulation']->smartTwinDetails();
        $flatRoof = $this->advices()['roof-insulation-flat-current']->smartTwinDetails();

        $this->assertSame(self::FLOOR, $floor->solutions[0]['id']);
        $this->assertSame(['floor', 'crawlspace'], array_keys($floor->elements));
        // The scenario leaves the floor alone, as in the example dossier.
        $this->assertNull($floor->elements['floor']['after']);

        $this->assertSame('flat', $flatRoof->elements['roof']['shape']);
        $this->assertEqualsWithDelta(30.73, $flatRoof->elements['roof']['insulated_area'], 0.001);
    }

    public function test_the_package_is_recorded_with_what_smarttwin_calculated_for_it(): void
    {
        $report = $this->import($this->response());

        // The subsidy is claimed, so the report says where it went instead of listing it as unmapped.
        $subsidyStatuses = collect($report->entries())
            ->filter(fn ($entry) => str_starts_with($entry->leaf->path, 'scenario.subsidies') || 'scenario.totalSubsidy' === $entry->leaf->path)
            ->map(fn ($entry) => $entry->status)
            ->unique()
            ->values()
            ->all();
        $this->assertSame([MappingStatus::SKIPPED], $subsidyStatuses);

        $package = $this->building->smartTwinPackage()->first();

        $this->assertEqualsCanonicalizing(
            array_map(fn (UserActionPlanAdvice $a) => $a->user_action_plan_advisable_id, array_values($this->advices())),
            $package->measure_application_ids,
        );
        $this->assertEqualsWithDelta(283.92, $package->gasSavings(), 0.001);
        $this->assertSame('B', $package->labelAfter());
        $this->assertEqualsWithDelta(15209.57, (float) $package->total_cost_with_tax, 0.001);
        $this->assertEqualsWithDelta(6853.75, (float) $package->total_subsidy, 0.001);
        $this->assertSame([['name' => 'ISDE', 'amount' => 6853.75]], $package->subsidies);
        $this->assertTrue($package->isIntactFor($package->measure_application_ids));
    }

    public function test_a_second_scan_replaces_the_package(): void
    {
        $this->import($this->response());
        $this->import($this->response(withFlatRoof: false));

        $this->assertEqualsCanonicalizing(['floor-insulation', 'roof-insulation-pitched-inside'], array_keys($this->advices()));
    }

    public function test_the_answers_follow_the_properties_whatever_the_solutions_say(): void
    {
        $report = $this->import($this->response());

        // The facade improves without a solution; its surface is written anyway.
        $this->assertEqualsWithDelta(40.98, (float) $this->answer('insulation-wall-surface'), 0.001);
        // The floor has a solution but does not improve; nothing is made up for it.
        $this->assertNull($this->answer('insulation-floor-surface'));
        $this->assertSame('yes', $this->answer('has-crawlspace'));

        $this->assertSame(2, $report->counts()[MappingStatus::SPEC_VIOLATION->value]);
    }

    /**
     * A dwelling with a well insulated cavity wall and pitched roof, for which SmartTwin advises only
     * the floor.
     */
    private function wellInsulatedResponse(float $facadeRc = 3.5): array
    {
        $current = $this->section($facadeRc, 3.5, null);
        $current['calculationResult'] = ['energyConsumptionGas' => 1200.0, 'energyLabel' => 'B'];

        $scenario = $this->section($facadeRc, 3.5, null);
        $scenario['solutions'] = [$this->solution(self::FLOOR, 3613.26)];
        $scenario['calculationResult'] = ['energyConsumptionGas' => 1100.0, 'energyLabel' => 'A'];

        return ['current' => $current, 'scenario' => $scenario, 'solutions' => []];
    }

    /**
     * @return array<string, string> Category by measure short, from master.
     */
    private function columns(): array
    {
        return collect($this->advices())->map(fn (UserActionPlanAdvice $advice) => $advice->category)->sort()->all();
    }

    public function test_what_is_good_and_not_advised_lands_in_order(): void
    {
        $this->import($this->wellInsulatedResponse());

        $this->assertSame([
            'cavity-wall-insulation'         => UserActionPlanAdviceService::CATEGORY_COMPLETE,
            'floor-insulation'               => UserActionPlanAdviceService::CATEGORY_TO_DO,
            'roof-insulation-pitched-inside' => UserActionPlanAdviceService::CATEGORY_COMPLETE,
        ], collect($this->columns())->sortKeys()->all());

        $inOrder = $this->advices()['cavity-wall-insulation'];
        $this->assertSame(AdviceSource::SMART_TWIN, $inOrder->source);
        $this->assertNull($inOrder->costs['to']);
        // Not part of the package SmartTwin calculated.
        $this->assertSame([MeasureApplication::findByShort('floor-insulation')->id], $this->building->smartTwinPackage()->first()->measure_application_ids);
    }

    public function test_reasonable_insulation_is_not_in_order(): void
    {
        // Redelijke isolatie: worth improving, so no card, even though SmartTwin does not advise it.
        $this->import($this->wellInsulatedResponse(facadeRc: 2.5));

        $this->assertArrayNotHasKey('cavity-wall-insulation', $this->advices());
    }

    public function test_an_import_without_advice_adds_no_cards_either(): void
    {
        $response = $this->wellInsulatedResponse();
        $response['scenario']['solutions'] = [];

        $this->import($response);

        $this->assertSame([], $this->advices());
    }

    public function test_an_executed_measure_gets_no_second_card(): void
    {
        // "Uitgevoerd" is offered once SmartTwin owns the woonplan.
        config()->set('hoomdossier.services.smarttwin.enabled', true);
        $this->import($this->wellInsulatedResponse());
        app(ExecutedAdviceService::class)->markExecuted($this->advices()['cavity-wall-insulation'], Carbon::parse('2026-10-01'));

        $this->import($this->wellInsulatedResponse());

        $rows = UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->building->user_id)
            ->where('user_action_plan_advisable_id', MeasureApplication::findByShort('cavity-wall-insulation')->id)
            ->get();
        $this->assertCount(3, $rows);
        $this->assertTrue($rows->every(fn (UserActionPlanAdvice $row) => $row->isExecuted()));
    }
}
