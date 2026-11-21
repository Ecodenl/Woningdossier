<?php

namespace Tests\Feature\app\Console\Commands\Upgrade;

use App\Models\Element;
use App\Models\ElementValue;
use App\Models\ToolQuestion;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * The seeder already produces the end state, so these tests take the new options out first, as on a
 * database deployed before them, and let the command and deploy:run put them back.
 */
final class AddSmartTwinGlazingOptionsTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private const COMMAND = 'upgrade:add-smart-twin-glazing-options';

    /** By element: the orders the command adds. */
    private const ADDED = [
        'living-rooms-windows'   => [4, 5, 6],
        'sleeping-rooms-windows' => [4, 5, 6],
        'frames'                 => [5, 6, 7, 8],
    ];

    /** @return array<string, array<int, array<string, mixed>>> The added options as the seeder makes them. */
    private function addedOptions(): array
    {
        $options = [];

        foreach (self::ADDED as $short => $orders) {
            $options[$short] = Element::findByShort($short)->values()
                ->whereIn('order', $orders)
                ->orderBy('order')
                ->get()
                ->map(fn ($value) => [
                    'order'           => $value->order,
                    'value'           => $value->getTranslation('value', 'nl'),
                    'calculate_value' => is_null($value->calculate_value) ? null : (float) $value->calculate_value,
                    'configurations'  => $value->configurations,
                ])
                ->all();
        }

        return $options;
    }

    private function takeOut(): void
    {
        foreach (self::ADDED as $short => $orders) {
            $ids = Element::findByShort($short)->values()->whereIn('order', $orders)->pluck('id');

            DB::table('tool_question_valuables')->where('tool_question_valuable_type', ElementValue::class)
                ->whereIn('tool_question_valuable_id', $ids)
                ->delete();
            DB::table('element_values')->whereIn('id', $ids)->delete();
        }
    }

    public function test_it_adds_what_the_seeder_would_and_nothing_twice(): void
    {
        $seeded = $this->addedOptions();
        $this->takeOut();

        $this->artisan(self::COMMAND, ['--dry-run' => true])->assertSuccessful();
        $this->assertSame([], array_merge(...array_values($this->addedOptions())));

        $this->artisan(self::COMMAND)->assertSuccessful();
        $this->assertEquals($seeded, $this->addedOptions());

        $this->artisan(self::COMMAND)->expectsOutput('Already done.')->assertSuccessful();
        $this->assertEquals($seeded, $this->addedOptions());
    }

    public function test_deploy_links_them_to_the_questions_hidden_from_the_scans(): void
    {
        $this->takeOut();

        $this->artisan('deploy:run')->assertSuccessful();

        $glass = ToolQuestion::findByShort('current-living-rooms-windows');
        $this->assertSame(['Enkelglas', 'Dubbelglas', 'HR++ glas', 'Drievoudige beglazing'], $glass->getQuestionValues()->pluck('name')->values()->all());
        $this->assertSame(['HR glas', 'HR+ glas', 'Vacuümglas'], $glass->getQuestionValues(true)->pluck('name')->slice(4)->values()->all());

        $frames = ToolQuestion::findByShort('frame-type');
        $this->assertCount(5, $frames->getQuestionValues());
        $this->assertCount(9, $frames->getQuestionValues(true));
    }
}
