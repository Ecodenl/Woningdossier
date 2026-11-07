<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Enums\GlassType;
use App\Helpers\ToolQuestionHelper;
use App\Models\Building;
use App\Models\Element;
use App\Models\ElementValue;
use App\Models\InputSource;
use App\Models\MeasureApplication;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\Econobis\Payloads\GebruikPayload;
use App\Services\ToolQuestionService;
use App\Services\UserActionPlanAdviceService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The glass and frame options only SmartTwin answers with: not offered by the scans, read back
 * everywhere, and glass in order from HR+. Decision 2026-11-07-smarttwin-glasmapping (B, C, D, E).
 */
final class GlazingOptionsTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private Building $building;

    protected function setUp(): void
    {
        parent::setUp();

        $this->building = User::first()->building;
    }

    public function test_the_scans_do_not_offer_the_options_only_smart_twin_answers_with(): void
    {
        foreach (['current-living-rooms-windows', 'current-sleeping-rooms-windows'] as $short) {
            $toolQuestion = ToolQuestion::findByShort($short);

            $this->assertSame(
                ['Enkelglas', 'Dubbelglas', 'HR++ glas', 'Drievoudige beglazing'],
                $toolQuestion->getQuestionValues()->pluck('name')->values()->all(),
            );
            $this->assertSame(
                ['Enkelglas', 'Dubbelglas', 'HR++ glas', 'Drievoudige beglazing', 'HR glas', 'HR+ glas', 'Vacuümglas'],
                $toolQuestion->getQuestionValues(true)->pluck('name')->values()->all(),
            );
        }

        $frameType = ToolQuestion::findByShort('frame-type');
        $this->assertCount(5, $frameType->getQuestionValues());
        $this->assertSame(
            ['Hout', 'Kunststof', 'Aluminium thermisch gescheiden', 'Aluminium niet thermisch gescheiden'],
            $frameType->getQuestionValues(true)->pluck('name')->slice(5)->values()->all(),
        );
    }

    public function test_the_new_glass_carries_its_comfort_and_insulation_factor(): void
    {
        $expected = [
            GlassType::HR->value      => ['comfort' => 3, 'insulation_factor' => 2],
            GlassType::HR_PLUS->value => ['comfort' => 4, 'insulation_factor' => 2],
            GlassType::VACUUM->value  => ['comfort' => 5, 'insulation_factor' => 4],
        ];

        foreach (['living-rooms-windows', 'sleeping-rooms-windows'] as $elementShort) {
            foreach ($expected as $order => $configurations) {
                $this->assertSame($configurations, $this->glass($elementShort, $order)->configurations);
            }
        }

        $frames = Element::findByShort('frames')->values()->where('order', '>', 4)->orderBy('order')->pluck('calculate_value');
        $this->assertEquals([1, 0, 0, 0], $frames->map(fn ($value) => (float) $value)->all());
    }

    public function test_an_answer_the_scans_do_not_offer_reads_back_with_its_name(): void
    {
        $toolQuestion = ToolQuestion::findByShort('current-living-rooms-windows');
        $hrPlus = $this->glass('living-rooms-windows', GlassType::HR_PLUS->value);
        $this->save('current-living-rooms-windows', $hrPlus->id);

        $master = InputSource::master();
        $this->assertSame('HR+ glas', ToolQuestionHelper::getHumanReadableAnswer($this->building, $master, $toolQuestion));

        $payload = app(GebruikPayload::class)->forBuilding($this->building)->buildPayload();
        $answers = collect($payload)->flatMap(fn ($situation) => $situation)->get('current-living-rooms-windows')['answers'];
        $this->assertSame('HR+ glas', $answers['name']);
    }

    public function test_glass_is_complete_from_hr_plus(): void
    {
        $measure = MeasureApplication::findByShort('hrpp-glass-only');

        $expectations = [
            GlassType::SINGLE->value       => UserActionPlanAdviceService::CATEGORY_TO_DO,
            GlassType::DOUBLE->value       => UserActionPlanAdviceService::CATEGORY_TO_DO,
            GlassType::HR->value           => UserActionPlanAdviceService::CATEGORY_TO_DO,
            GlassType::HR_PLUS->value      => UserActionPlanAdviceService::CATEGORY_COMPLETE,
            GlassType::HR_PLUS_PLUS->value => UserActionPlanAdviceService::CATEGORY_COMPLETE,
            GlassType::TRIPLE->value       => UserActionPlanAdviceService::CATEGORY_COMPLETE,
            GlassType::VACUUM->value       => UserActionPlanAdviceService::CATEGORY_COMPLETE,
        ];

        foreach ($expectations as $order => $category) {
            $this->save('current-living-rooms-windows', $this->glass('living-rooms-windows', $order)->id);
            $this->save('current-sleeping-rooms-windows', $this->glass('sleeping-rooms-windows', $order)->id);

            $this->assertSame(
                $category,
                UserActionPlanAdviceService::getCategoryFromMeasure($this->building->fresh(), $measure),
                GlassType::from($order)->name,
            );
        }
    }

    public function test_the_worst_glass_decides_the_category(): void
    {
        $measure = MeasureApplication::findByShort('hrpp-glass-only');
        $this->save('current-living-rooms-windows', $this->glass('living-rooms-windows', GlassType::VACUUM->value)->id);
        $this->save('current-sleeping-rooms-windows', $this->glass('sleeping-rooms-windows', GlassType::HR->value)->id);

        $this->assertSame(
            UserActionPlanAdviceService::CATEGORY_TO_DO,
            UserActionPlanAdviceService::getCategoryFromMeasure($this->building->fresh(), $measure),
        );
    }

    public function test_the_door_and_panel_questions_are_asked_by_no_scan_and_reach_econobis(): void
    {
        $answers = [
            'total-door-surface'  => 10.92,
            'door-quality'        => 'not-insulated',
            'total-panel-surface' => 4.5,
            'panel-quality'       => 'unknown',
            'panel-frame-type'    => 'metal-thermally-broken',
        ];
        $names = [
            'total-door-surface'  => '10,9', // One decimal, as every surface shows
            'door-quality'        => 'Ongeïsoleerd',
            'total-panel-surface' => '4,5',
            'panel-quality'       => 'Onbekend',
            'panel-frame-type'    => 'Metaal, thermisch onderbroken',
        ];

        foreach ($answers as $short => $answer) {
            $this->assertCount(0, ToolQuestion::findByShort($short)->subSteps, "{$short} is asked by a scan");
            $this->save($short, $answer);
        }

        $master = InputSource::master();
        foreach ($names as $short => $name) {
            $this->assertStringStartsWith(
                $name,
                (string) ToolQuestionHelper::getHumanReadableAnswer($this->building, $master, ToolQuestion::findByShort($short)),
                $short,
            );
        }

        $payload = app(GebruikPayload::class)->forBuilding($this->building)->buildPayload();
        $sent = collect($payload)->flatMap(fn ($situation) => $situation);
        $this->assertSame('not-insulated', $sent->get('door-quality')['answers']['short']);
        $this->assertSame('Metaal, thermisch onderbroken', $sent->get('panel-frame-type')['answers']['name']);
        $this->assertEquals(10.92, $sent->get('total-door-surface')['answers']['value']);
    }

    private function glass(string $elementShort, int $order): ElementValue
    {
        return Element::findByShort($elementShort)->values()->where('order', $order)->firstOrFail();
    }

    private function save(string $short, mixed $answer): void
    {
        ToolQuestionService::init()
            ->toolQuestion(ToolQuestion::findByShort($short))
            ->building($this->building)
            ->currentInputSource(InputSource::resident())
            ->save($answer);
    }
}
