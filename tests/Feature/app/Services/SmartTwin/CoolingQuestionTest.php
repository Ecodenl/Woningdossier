<?php

namespace Tests\Feature\app\Services\SmartTwin;

use App\Helpers\ToolQuestionHelper;
use App\Models\InputSource;
use App\Models\ToolQuestion;
use App\Models\User;
use App\Services\Econobis\Payloads\GebruikPayload;
use App\Services\ToolQuestionService;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cooling, which only SmartTwin fills: asked by no scan, read back, and sent to Econobis. Decision
 * 2026-11-07-smarttwin-installaties-mapping (K1).
 */
final class CoolingQuestionTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    public function test_cooling_is_asked_by_no_scan_and_reaches_econobis(): void
    {
        $building = User::first()->building;
        $cooling = ToolQuestion::findByShort('cooling-type');

        $this->assertCount(0, $cooling->subSteps);

        ToolQuestionService::init()
            ->toolQuestion($cooling)
            ->building($building)
            ->currentInputSource(InputSource::resident())
            ->save(['compression', 'free-cooling']);

        $this->assertSame(
            'Compressiekoelmachine, Vrije koeling',
            ToolQuestionHelper::getHumanReadableAnswer($building, InputSource::master(), $cooling),
        );

        $payload = app(GebruikPayload::class)->forBuilding($building)->buildPayload();
        $sent = collect($payload)->flatMap(fn ($situation) => $situation)->get('cooling-type')['answers'];
        $this->assertSame(['compression', 'free-cooling'], array_column($sent, 'short'));
    }
}
