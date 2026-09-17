<?php

namespace Tests\Feature\app\Console\Commands\Upgrade;

use App\Models\Element;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * The seeder already produces the end state, so these tests undo it first and let the command put
 * it back. That is the situation on an existing database, and the only one where the command has
 * anything to do.
 */
final class AddReasonableInsulationValueTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private const SHORTS = ['wall-insulation', 'floor-insulation', 'roof-insulation'];

    /**
     * The inverse of what the command does: drop "Redelijke isolatie" and pull everything above it
     * back down. Ascending this time, for the same reason the command goes descending.
     *
     * @var array<string, array<int, array<int, int>>>  short => [[fromCalculateValue, order, calculateValue]]
     */
    private const REVERT = [
        'wall-insulation' => [[5, 3, 4], [6, 5, 5]],
        'floor-insulation' => [[5, 3, 4], [6, 4, 5], [7, 5, 6]],
        'roof-insulation' => [[5, 3, 4], [6, 4, 5], [7, 5, 6]],
    ];

    private function revertToStateBeforeTheUpgrade(): void
    {
        foreach (self::REVERT as $short => $shifts) {
            $elementId = Element::findByShort($short)->id;

            DB::table('element_values')
                ->where('element_id', $elementId)
                ->where('calculate_value', 4)
                ->delete();

            foreach ($shifts as [$from, $order, $calculateValue]) {
                DB::table('element_values')
                    ->where('element_id', $elementId)
                    ->where('calculate_value', $from)
                    ->update(['order' => $order, 'calculate_value' => $calculateValue]);
            }

            Element::clearShortCache($short);
        }
    }

    /** @return array<int, array<int, int>> */
    private function stateOf(string $short): array
    {
        return DB::table('element_values')
            ->where('element_id', Element::findByShort($short)->id)
            ->orderBy('order')
            ->get(['order', 'calculate_value'])
            ->map(fn ($row) => [(int) $row->order, (int) $row->calculate_value])
            ->all();
    }

    private function valueAt(string $short, int $calculateValue): ?object
    {
        return DB::table('element_values')
            ->where('element_id', Element::findByShort($short)->id)
            ->where('calculate_value', $calculateValue)
            ->first();
    }

    public function test_it_adds_the_value_to_wall_floor_and_roof(): void
    {
        $this->revertToStateBeforeTheUpgrade();

        $this->artisan('upgrade:add-reasonable-insulation-value')->assertSuccessful();

        foreach (self::SHORTS as $short) {
            $added = $this->valueAt($short, 4);

            $this->assertNotNull($added, "geen nieuwe waarde voor {$short}");
            $this->assertSame(3, (int) $added->order);
            $this->assertSame('Redelijke isolatie', json_decode($added->value, true)['nl']);
        }
    }

    public function test_it_behaves_as_good_for_the_heat_pump_score(): void
    {
        // Not higher: the score is looked up in key_figure_insulation_factors, which stops at 4.0.
        $this->revertToStateBeforeTheUpgrade();

        $this->artisan('upgrade:add-reasonable-insulation-value')->assertSuccessful();

        foreach (self::SHORTS as $short) {
            $configurations = json_decode($this->valueAt($short, 4)->configurations, true);

            $this->assertSame(3, $configurations['insulation_factor']);
            $this->assertSame(3, $configurations['comfort']);
        }
    }

    public function test_it_leaves_the_scale_ordered(): void
    {
        $this->revertToStateBeforeTheUpgrade();

        $this->artisan('upgrade:add-reasonable-insulation-value')->assertSuccessful();

        $this->assertSame([[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6]], $this->stateOf('wall-insulation'));
        $this->assertSame([[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6], [6, 7]], $this->stateOf('floor-insulation'));
        $this->assertSame([[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6], [6, 7]], $this->stateOf('roof-insulation'));
    }

    public function test_it_ends_up_where_the_seeder_would_have_put_it(): void
    {
        // A fresh install gets this state from the seeder alone. If the two ever drift apart, the
        // seeder starts inserting duplicates next to what the command produced.
        $seeded = array_map(fn (string $short) => $this->stateOf($short), self::SHORTS);

        $this->revertToStateBeforeTheUpgrade();
        $this->artisan('upgrade:add-reasonable-insulation-value')->assertSuccessful();

        $this->assertSame($seeded, array_map(fn (string $short) => $this->stateOf($short), self::SHORTS));
    }

    public function test_an_answer_keeps_pointing_at_the_option_it_pointed_at(): void
    {
        // Answers reference element_value_id, so renumbering must not move them. "Goede isolatie"
        // changes from calculate value 4 to 5; the row itself, and the answer, stay put.
        $this->revertToStateBeforeTheUpgrade();

        $good = $this->valueAt('wall-insulation', 4);

        $this->artisan('upgrade:add-reasonable-insulation-value')->assertSuccessful();

        $stillThere = DB::table('element_values')->where('id', $good->id)->first();

        $this->assertNotNull($stillThere);
        $this->assertSame(5, (int) $stillThere->calculate_value);
        $this->assertSame(json_decode($good->value, true), json_decode($stillThere->value, true));
    }

    public function test_running_it_again_changes_nothing(): void
    {
        $this->revertToStateBeforeTheUpgrade();
        $this->artisan('upgrade:add-reasonable-insulation-value')->assertSuccessful();

        $after = array_map(fn (string $short) => $this->stateOf($short), self::SHORTS);

        $this->artisan('upgrade:add-reasonable-insulation-value')
            ->expectsOutputToContain('already done')
            ->assertSuccessful();

        $this->assertSame($after, array_map(fn (string $short) => $this->stateOf($short), self::SHORTS));
    }

    public function test_a_dry_run_writes_nothing(): void
    {
        $this->revertToStateBeforeTheUpgrade();

        $before = array_map(fn (string $short) => $this->stateOf($short), self::SHORTS);

        $this->artisan('upgrade:add-reasonable-insulation-value --dry-run')->assertSuccessful();

        $this->assertSame($before, array_map(fn (string $short) => $this->stateOf($short), self::SHORTS));
    }

    public function test_it_refuses_to_touch_a_scale_it_does_not_recognise(): void
    {
        $this->revertToStateBeforeTheUpgrade();

        DB::table('element_values')
            ->where('element_id', Element::findByShort('wall-insulation')->id)
            ->where('calculate_value', 3)
            ->delete();

        $mangled = $this->stateOf('wall-insulation');

        $this->artisan('upgrade:add-reasonable-insulation-value')->assertFailed();

        // Aborts on the first element it does not recognise, before writing anything anywhere.
        $this->assertSame($mangled, $this->stateOf('wall-insulation'));
        $this->assertNull($this->valueAt('floor-insulation', 7));
    }
}
