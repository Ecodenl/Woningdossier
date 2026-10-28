<?php

namespace Tests\Feature\app\Models;

use App\Models\MeasureApplication;
use App\Models\Step;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\LegacyService;
use App\Services\UserActionPlanAdviceService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The measures that only exist for SmartTwin's solutions to be coupled to (stuurgroep, 16 October
 * 2026). They reach a woonplan through the SmartTwin import alone, so nothing else in Hoomdossier
 * may pick them up: no questions, and nothing on the old expert pages, where an unknown measure on
 * the glazing step breaks the save and one on the roof step fails its validation.
 */
final class ExternallyAdvisedMeasuresTest extends TestCase
{
    use RefreshDatabase;

    public bool $seed = true;
    public string $seeder = DatabaseSeeder::class;

    /** Measure short => the short of its step. */
    private const MEASURES = [
        'sun-shading'                            => 'insulated-glazing',
        'exterior-wall-insulation'               => 'wall-insulation',
        'roof-insulation-flat-inside'            => 'roof-insulation',
        'collective-combi-heat-pump-outside-air' => 'heating',
        'double-glass-only'                      => 'insulated-glazing',
        'double-glass-frames'                    => 'insulated-glazing',
        'vacuum-glass-only'                      => 'insulated-glazing',
        'vacuum-glass-frames'                    => 'insulated-glazing',
        'solar-control-hrpp-glass-only'          => 'insulated-glazing',
        'solar-control-hrpp-glass-frames'        => 'insulated-glazing',
        'solar-control-triple-glass-frames'      => 'insulated-glazing',
        'shower-heat-recovery'                   => 'heating',
        'district-heating-connect'               => 'heating',
        'high-temperature-radiators'             => 'heating',
        'low-temperature-radiators'              => 'heating',
        'floor-heating'                          => 'heating',
        'induction-hob'                          => 'heating',
    ];

    public function test_they_are_seeded_as_externally_advised_on_their_step(): void
    {
        $seeded = MeasureApplication::measureType(MeasureApplication::EXTERNALLY_ADVISED)
            ->with('step')
            ->get()
            ->mapWithKeys(fn (MeasureApplication $measure) => [$measure->short => $measure->step->short])
            ->all();

        $this->assertEqualsCanonicalizing(self::MEASURES, $seeded);

        foreach (MeasureApplication::measureType(MeasureApplication::EXTERNALLY_ADVISED)->get() as $measure) {
            $this->assertNotEmpty($measure->name, $measure->short);
            $this->assertNotEmpty($measure->info, $measure->short);
            $this->assertNotEmpty($measure->configurations['icon'] ?? null, $measure->short);
            $this->assertArrayHasKey('comfort', $measure->configurations, $measure->short);
        }
    }

    public function test_they_get_no_questions(): void
    {
        foreach (array_keys(self::MEASURES) as $short) {
            foreach (["user-costs-{$short}-own-total", "user-costs-{$short}-subsidy-total", "execute-{$short}-how"] as $question) {
                $this->assertNull(ToolQuestion::findByShort($question), $question);
            }
        }
    }

    public function test_the_old_expert_pages_keep_to_their_own_measures(): void
    {
        $legacy = new LegacyService();
        $shortsOn = fn (string $step) => MeasureApplication::whereIn('id', array_keys($legacy->getToolQuestionShorts(Step::findByShort($step))))
            ->pluck('short')
            ->all();

        // The glazing page posts a choice for these four only, and its save reads one for every
        // measure listed here.
        $this->assertEqualsCanonicalizing(
            ['glass-in-lead', 'hrpp-glass-only', 'hrpp-glass-frames', 'hr3p-frames'],
            $shortsOn('insulated-glazing'),
        );
        // The roof page shows these four only, and validates the questions of every measure here.
        $this->assertEqualsCanonicalizing(
            ['roof-insulation-pitched-inside', 'roof-insulation-pitched-replace-tiles', 'roof-insulation-flat-current', 'roof-insulation-flat-replace-current'],
            $shortsOn('roof-insulation'),
        );
        $this->assertEqualsCanonicalizing(
            ['cavity-wall-insulation', 'facade-wall-insulation', 'wall-insulation-research'],
            $shortsOn('wall-insulation'),
        );
    }

    public function test_the_category_logic_leaves_them_in_to_do(): void
    {
        $building = User::first()->building;

        foreach (array_keys(self::MEASURES) as $short) {
            $this->assertSame(
                UserActionPlanAdviceService::CATEGORY_TO_DO,
                UserActionPlanAdviceService::getCategoryFromMeasure($building, MeasureApplication::findByShort($short)),
                $short,
            );
        }
    }
}
