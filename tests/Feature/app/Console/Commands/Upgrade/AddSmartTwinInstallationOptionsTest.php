<?php

namespace Tests\Feature\app\Console\Commands\Upgrade;

use App\Models\PvPanelOrientation;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\ToolQuestion;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * The heat pump types and panel orientations SmartTwin can describe: not offered by the scans, read
 * back everywhere, and added to an existing database on deploy. Decision
 * 2026-11-07-smarttwin-installaties-mapping (V1, P-nieuw).
 */
final class AddSmartTwinInstallationOptionsTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private const COMMAND = 'upgrade:add-smart-twin-installation-options';

    private const ADDED_HEAT_PUMPS = [
        'Hybride warmtepomp met bodemwarmte', 'Hybride warmtepomp met grondwater', 'Hybride warmtepomp met oppervlaktewater',
        'Hybride warmtepomp met HT-bron', 'Hybride warmtepomp, bron onbekend', 'Volledige warmtepomp met ventilatielucht',
        'Volledige warmtepomp met grondwater', 'Volledige warmtepomp met oppervlaktewater', 'Volledige warmtepomp met HT-bron',
        'Volledige warmtepomp, bron onbekend',
    ];

    /** @return array<int, string> */
    private function names(string $short, bool $withHidden): array
    {
        return ToolQuestion::findByShort($short)->getQuestionValues($withHidden)->pluck('name')->map(fn ($name) => strip_tags($name))->values()->all();
    }

    public function test_the_scans_do_not_offer_them(): void
    {
        $this->assertCount(6, $this->names('heat-pump-type', false));
        $this->assertSame(self::ADDED_HEAT_PUMPS, array_slice($this->names('heat-pump-type', true), 6));

        $this->assertCount(6, $this->names('new-heat-pump-type', false));
        $this->assertSame(self::ADDED_HEAT_PUMPS, array_slice($this->names('new-heat-pump-type', true), 6));

        foreach (['solar-panel-orientation', 'heater-pv-panel-orientation'] as $short) {
            $this->assertCount(13, $this->names($short, false), $short);
            $this->assertSame(['horizontaal', 'noord'], array_slice($this->names($short, true), 13), $short);
        }
    }

    public function test_the_calculations_see_no_calculate_value_on_them(): void
    {
        $added = ServiceValue::where('service_id', Service::findByShort('heat-pump')->id)->where('order', '>', 6)->get();

        $this->assertCount(10, $added);
        $this->assertSame([null], $added->pluck('calculate_value')->unique()->values()->all());
    }

    public function test_the_command_adds_what_the_seeders_would_and_nothing_twice(): void
    {
        $heatPump = Service::findByShort('heat-pump');
        $seeded = $this->snapshot();
        $this->takeOut();

        $this->artisan(self::COMMAND, ['--dry-run' => true])->assertSuccessful();
        $this->assertSame(['heat-pump' => [], 'orientations' => []], $this->snapshot());

        $this->artisan(self::COMMAND)->assertSuccessful();
        $this->assertEquals($seeded, $this->snapshot());

        $this->artisan(self::COMMAND)->expectsOutput('Already done.')->assertSuccessful();
        $this->assertEquals($seeded, $this->snapshot());
        $this->assertSame(16, ServiceValue::where('service_id', $heatPump->id)->count());
    }

    public function test_deploy_links_them_to_the_questions_hidden_from_the_scans(): void
    {
        $this->takeOut();

        $this->artisan('deploy:run')->assertSuccessful();

        $this->assertSame(self::ADDED_HEAT_PUMPS, array_slice($this->names('heat-pump-type', true), 6));
        $this->assertCount(6, $this->names('heat-pump-type', false));
        $this->assertSame(['horizontaal', 'noord'], array_slice($this->names('solar-panel-orientation', true), 13));
        $this->assertCount(13, $this->names('solar-panel-orientation', false));
    }

    /** @return array{heat-pump: array<int, array<string, mixed>>, orientations: array<int, array<string, mixed>>} */
    private function snapshot(): array
    {
        return [
            'heat-pump' => ServiceValue::where('service_id', Service::findByShort('heat-pump')->id)->where('order', '>', 6)
                ->orderBy('order')->get()
                ->map(fn (ServiceValue $value) => [$value->order, $value->getTranslation('value', 'nl'), $value->calculate_value, $value->configurations])
                ->all(),
            'orientations' => PvPanelOrientation::whereIn('short', ['horizontal', 'n'])->orderBy('order')->get()
                ->map(fn (PvPanelOrientation $orientation) => [$orientation->short, $orientation->getTranslation('name', 'nl'), $orientation->order])
                ->all(),
        ];
    }

    private function takeOut(): void
    {
        $values = ServiceValue::where('service_id', Service::findByShort('heat-pump')->id)->where('order', '>', 6)->pluck('id');
        $orientations = PvPanelOrientation::whereIn('short', ['horizontal', 'n'])->pluck('id');

        DB::table('tool_question_valuables')->where('tool_question_valuable_type', ServiceValue::class)->whereIn('tool_question_valuable_id', $values)->delete();
        DB::table('tool_question_valuables')->where('tool_question_valuable_type', PvPanelOrientation::class)->whereIn('tool_question_valuable_id', $orientations)->delete();
        DB::table('service_values')->whereIn('id', $values)->delete();
        DB::table('pv_panel_orientations')->whereIn('id', $orientations)->delete();
    }
}
