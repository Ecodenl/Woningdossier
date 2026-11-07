<?php

namespace Tests\Feature\app\Services;

use App\Enums\AdviceSource;
use App\Helpers\Cooperation\Tool\SmallMeasureHelper;
use App\Helpers\MediaHelper;
use App\Jobs\ProcessRecalculate;
use App\Jobs\SmartTwin\UndoSmartTwinForBuilding;
use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\Scan;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Models\UserActionPlanAdvice;
use App\Services\ExecutedAdviceService;
use App\Services\SmartTwin\Mapping\MappingApplier;
use App\Services\SmartTwin\Mapping\MappingResult;
use App\Services\ToolQuestionService;
use App\Services\UserActionPlanAdviceService;
use Database\Seeders\DatabaseSeeder;
use DomainException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * "Uitgevoerd": offered once SmartTwin owns the woonplan, and from then on the same for resident,
 * coach and master. The calculation never removes or moves an executed measure, with or without
 * SmartTwin, so the old woonplan can get it later by widening one check.
 */
final class ExecutedAdviceServiceTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private Cooperation $cooperation;
    private User $user;
    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('hoomdossier.services.smarttwin.enabled', true);

        $this->cooperation = Cooperation::factory()->create();
        $this->cooperation->scans()->attach(Scan::findByShort(Scan::QUICK)->id);

        $this->user = User::factory()->withAccount()->asResident()
            ->create(['cooperation_id' => $this->cooperation->id, 'allow_access' => true]);
        $this->building = Building::factory()->create(['user_id' => $this->user->id]);
    }

    private function service(): ExecutedAdviceService
    {
        return app(ExecutedAdviceService::class);
    }

    /**
     * A SmartTwin package card, as an import leaves it: the same for all three input sources.
     */
    private function packageCard(string $measureShort = 'cavity-wall-insulation'): UserActionPlanAdvice
    {
        $applier = app(MappingApplier::class);
        $applier->startRun();
        $applier->apply($this->building, InputSource::resident(), MappingResult::mappedAdvice($measureShort, 1622.65));

        return $this->rows($measureShort)->firstWhere('input_source_id', InputSource::master()->id);
    }

    /**
     * @return Collection<int, UserActionPlanAdvice>
     */
    private function rows(string $measureShort): Collection
    {
        return UserActionPlanAdvice::withoutGlobalScopes()
            ->where('user_id', $this->user->id)
            ->where('user_action_plan_advisable_type', MeasureApplication::class)
            ->where('user_action_plan_advisable_id', MeasureApplication::findByShort($measureShort)->id)
            ->get();
    }

    /**
     * A calculated small measure marked as executed directly, as the old woonplan would once it gets
     * "uitgevoerd". For the guards that have to hold without SmartTwin.
     */
    private function executedSmallMeasure(): void
    {
        foreach ([InputSource::resident(), InputSource::master()] as $inputSource) {
            UserActionPlanAdvice::withoutEvents(fn () => UserActionPlanAdvice::withoutGlobalScopes()->forceCreate([
                'user_id'                         => $this->user->id,
                'input_source_id'                 => $inputSource->id,
                'user_action_plan_advisable_type' => MeasureApplication::class,
                'user_action_plan_advisable_id'   => MeasureApplication::findByShort('save-energy-with-light')->id,
                'step_id'                         => Step::findByShort('small-measures')->id,
                'costs'                           => ['from' => 10, 'to' => 45],
                'category'                        => UserActionPlanAdviceService::CATEGORY_COMPLETE,
                'visible'                         => true,
                'executed_at'                     => '2026-09-01',
            ]));
        }
    }

    public function test_marking_executed_files_the_card_in_order_for_everyone(): void
    {
        $master = $this->packageCard();

        $this->service()->markExecuted($master, Carbon::parse('2026-10-01'), 'Spouwmuur gevuld door Isoleer BV');

        $rows = $this->rows('cavity-wall-insulation');
        $this->assertCount(3, $rows);
        foreach ($rows as $row) {
            $this->assertSame('2026-10-01', $row->executed_at->toDateString());
            $this->assertSame('Spouwmuur gevuld door Isoleer BV', $row->executed_description);
            $this->assertSame(UserActionPlanAdviceService::CATEGORY_COMPLETE, $row->category);
        }
    }

    public function test_undoing_puts_the_card_back_to_do(): void
    {
        $master = $this->packageCard();
        $this->service()->markExecuted($master, Carbon::parse('2026-10-01'));

        $this->service()->undoExecuted($master);

        foreach ($this->rows('cavity-wall-insulation') as $row) {
            $this->assertNull($row->executed_at);
            $this->assertSame(UserActionPlanAdviceService::CATEGORY_TO_DO, $row->category);
        }
    }

    public function test_before_the_switch_it_is_not_offered(): void
    {
        $this->assertFalse(ExecutedAdviceService::isAvailableFor($this->building));

        $advice = UserActionPlanAdvice::withoutGlobalScopes()->create([
            'user_id'                         => $this->user->id,
            'input_source_id'                 => InputSource::resident()->id,
            'user_action_plan_advisable_type' => MeasureApplication::class,
            'user_action_plan_advisable_id'   => MeasureApplication::findByShort('cavity-wall-insulation')->id,
            'step_id'                         => MeasureApplication::findByShort('cavity-wall-insulation')->step_id,
            'costs'                           => ['from' => null, 'to' => 1000],
        ]);

        $this->expectException(DomainException::class);
        $this->service()->markExecuted($advice, Carbon::parse('2026-10-01'));
    }

    public function test_a_document_lands_on_the_building_as_an_invoice(): void
    {
        Storage::fake('uploads');
        $master = $this->packageCard();
        $this->service()->markExecuted($master, Carbon::parse('2026-10-01'));

        $path = tempnam(sys_get_temp_dir(), 'invoice') . '.pdf';
        file_put_contents($path, '%PDF-1.4 test');
        $media = $this->service()->attachDocument($master, $path, 'factuur-spouwmuur', InputSource::resident());
        @unlink($path);

        $this->assertSame(MediaHelper::INVOICE, $this->building->media()->first()->pivot->tag);
        $this->assertTrue((bool) data_get($media->custom_properties, 'share_with_cooperation'));
        // Found from any row of the measure: the link is the measure, not a row.
        $residentRow = $this->rows('cavity-wall-insulation')->firstWhere('input_source_id', InputSource::resident()->id);
        $this->assertSame([$media->id], $this->service()->documentsOf($residentRow)->pluck('id')->all());
    }

    public function test_a_document_takes_only_a_building_tag(): void
    {
        $master = $this->packageCard();

        $this->expectException(InvalidArgumentException::class);
        $this->service()->attachDocument($master, '/dev/null', 'logo', InputSource::resident(), MediaHelper::LOGO);
    }

    public function test_without_smart_twin_clearing_a_step_keeps_an_executed_measure(): void
    {
        $this->cooperation->update(['smarttwin_enabled' => false]);
        $this->executedSmallMeasure();

        UserActionPlanAdviceService::clearForStep($this->user, InputSource::resident(), Step::findByShort('small-measures'));

        $this->assertCount(2, $this->rows('save-energy-with-light'));
    }

    public function test_without_smart_twin_a_recalculation_neither_duplicates_nor_moves_an_executed_measure(): void
    {
        $this->cooperation->update(['smarttwin_enabled' => false]);
        $this->executedSmallMeasure();

        // The resident still says "want to": the calculation would advise it in "Nu aanpakken".
        ToolQuestionService::init()
            ->toolQuestion(ToolQuestion::findByShort('apply-led-light'))
            ->building($this->building)
            ->currentInputSource(InputSource::resident())
            ->save('want-to');
        (new SmallMeasureHelper($this->user->fresh(), InputSource::resident()))->withoutOldAdvices()->createValues()->createAdvices();

        $rows = $this->rows('save-energy-with-light');
        $this->assertCount(2, $rows);
        $this->assertTrue($rows->every(fn (UserActionPlanAdvice $row) => UserActionPlanAdviceService::CATEGORY_COMPLETE === $row->category));
    }

    public function test_an_import_that_advises_it_again_puts_it_back_in_the_package_with_its_record(): void
    {
        $master = $this->packageCard();
        $this->service()->markExecuted($master, Carbon::parse('2026-10-01'), 'Gedaan');

        $applier = app(MappingApplier::class);
        $applier->startRun();
        $applier->apply($this->building, InputSource::resident(), MappingResult::mappedAdvice('cavity-wall-insulation', 1800.0));

        $after = $this->rows('cavity-wall-insulation')->firstWhere('input_source_id', InputSource::master()->id);
        // The same row: what is tied to the advice stays tied to it.
        $this->assertSame($master->id, $after->id);
        $this->assertSame(UserActionPlanAdviceService::CATEGORY_TO_DO, $after->category);
        $this->assertEqualsWithDelta(1800.0, (float) $after->costs['to'], 0.001);
        $this->assertSame('2026-10-01', $after->executed_at->toDateString());
        $this->assertSame('Gedaan', $after->executed_description);
    }

    public function test_an_import_that_does_not_advise_it_leaves_it_in_order(): void
    {
        $master = $this->packageCard();
        $this->service()->markExecuted($master, Carbon::parse('2026-10-01'));

        $applier = app(MappingApplier::class);
        $applier->startRun();
        $applier->apply($this->building, InputSource::resident(), MappingResult::mappedAdvice('floor-insulation', 2000.0));

        $rows = $this->rows('cavity-wall-insulation');
        $this->assertCount(3, $rows);
        $this->assertTrue($rows->every(fn (UserActionPlanAdvice $row) => UserActionPlanAdviceService::CATEGORY_COMPLETE === $row->category && $row->isExecuted()));
    }

    public function test_switching_smart_twin_off_keeps_an_executed_measure_as_the_residents_own(): void
    {
        Bus::fake([ProcessRecalculate::class]);
        $master = $this->packageCard();
        $this->service()->markExecuted($master, Carbon::parse('2026-10-01'));

        $this->cooperation->update(['smarttwin_enabled' => false]);
        UndoSmartTwinForBuilding::dispatchSync($this->building->fresh());

        $rows = $this->rows('cavity-wall-insulation');
        $this->assertCount(3, $rows);
        $this->assertTrue($rows->every(fn (UserActionPlanAdvice $row) => is_null($row->source) && $row->isExecuted()));
    }
}
