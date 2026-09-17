<?php

namespace Tests\Feature\app\Console\Commands\Upgrade;

use App\Models\Element;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * The seeder already produces the end state, so these tests undo it first and let the command put
 * it back. Two starting points matter: a database that never ran the command, and one that ran it
 * when it only added Redelijke isolatie — which is where staging stands.
 */
final class ExtendInsulationScalesTest extends TestCase
{
    use RefreshDatabase;

    public $seed = true;
    public $seeder = DatabaseSeeder::class;

    private const COMMAND = 'upgrade:extend-insulation-scales';
    private const SHORTS = ['wall-insulation', 'floor-insulation', 'roof-insulation'];

    private const TARGET = [
        'wall-insulation'  => [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6]],
        'floor-insulation' => [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6], [6, 7], [7, 8]],
        'roof-insulation'  => [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6], [6, 7]],
    ];

    /**
     * Walking the seeder's end state back: which levels to drop, then where the rest belongs.
     *
     * @var array<string, array{0: array<int, int>, 1: array<int, array<int, int>>}>
     */
    private const TO_ORIGINAL = [
        'wall-insulation'  => [[4], [[5, 3, 4], [6, 5, 5]]],
        'floor-insulation' => [[3, 5], [[4, 2, 3], [6, 3, 4], [7, 4, 5], [8, 5, 6]]],
        'roof-insulation'  => [[4], [[5, 3, 4], [6, 4, 5], [7, 5, 6]]],
    ];

    /** The same, but stopping where staging stands: Redelijk applied, Slecht not. */
    private const TO_STAGING = [
        'floor-insulation' => [[3], [[4, 2, 3], [5, 3, 4], [6, 4, 5], [7, 5, 6], [8, 6, 7]]],
    ];

    /**
     * @param  array<string, array{0: array<int, int>, 1: array<int, array<int, int>>}>  $map
     */
    private function rewind(array $map): void
    {
        foreach ($map as $short => [$drop, $shifts]) {
            $elementId = Element::findByShort($short)->id;

            DB::table('element_values')
                ->where('element_id', $elementId)
                ->whereIn('calculate_value', $drop)
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

    private function levelNamed(string $short, string $label): ?object
    {
        return DB::table('element_values')
            ->where('element_id', Element::findByShort($short)->id)
            ->get()
            ->first(fn ($row) => $label === (json_decode($row->value, true)['nl'] ?? null));
    }

    /** @return array<string, array<int, array<int, int>>> */
    private function allStates(): array
    {
        $states = [];

        foreach (self::SHORTS as $short) {
            $states[$short] = $this->stateOf($short);
        }

        return $states;
    }

    public function test_it_brings_every_scale_to_its_own_end_state(): void
    {
        $this->rewind(self::TO_ORIGINAL);

        $this->artisan(self::COMMAND)->assertSuccessful();

        foreach (self::TARGET as $short => $target) {
            $this->assertSame($target, $this->stateOf($short), $short);
        }
    }

    public function test_reasonable_is_added_to_all_three(): void
    {
        $this->rewind(self::TO_ORIGINAL);
        $this->artisan(self::COMMAND)->assertSuccessful();

        foreach (self::SHORTS as $short) {
            $this->assertNotNull($this->levelNamed($short, 'Redelijke isolatie'), $short);
        }
    }

    public function test_poor_is_added_to_the_floor_only(): void
    {
        // It belongs to the floor classification; the wall table retired it and the roof has no
        // table yet.
        $this->rewind(self::TO_ORIGINAL);
        $this->artisan(self::COMMAND)->assertSuccessful();

        $this->assertNotNull($this->levelNamed('floor-insulation', 'Slechte isolatie'));
        $this->assertNull($this->levelNamed('wall-insulation', 'Slechte isolatie'));
        $this->assertNull($this->levelNamed('roof-insulation', 'Slechte isolatie'));
    }

    public function test_poor_sits_between_none_and_moderate(): void
    {
        $this->rewind(self::TO_ORIGINAL);
        $this->artisan(self::COMMAND)->assertSuccessful();

        $poor = $this->levelNamed('floor-insulation', 'Slechte isolatie');

        $this->assertSame(3, (int) $poor->calculate_value);
        $this->assertSame(2, (int) $poor->order);
    }

    public function test_poor_still_counts_as_a_floor_worth_insulating(): void
    {
        // Which is the whole reason it sits at 3 and the rest shifted up: ElementValue draws the
        // line between needing insulation and having it at 4 for the floor.
        $this->rewind(self::TO_ORIGINAL);
        $this->artisan(self::COMMAND)->assertSuccessful();

        $poor = \App\Models\ElementValue::find($this->levelNamed('floor-insulation', 'Slechte isolatie')->id);

        $this->assertFalse($poor->countsAsInsulated());
        $this->assertTrue(\App\Models\ElementValue::find($this->levelNamed('floor-insulation', 'Matige isolatie (tot 8 cm isolatie)')->id)->countsAsInsulated());
    }

    public function test_the_new_levels_behave_as_decided(): void
    {
        $this->rewind(self::TO_ORIGINAL);
        $this->artisan(self::COMMAND)->assertSuccessful();

        // Redelijk as Goed, so a dwelling scoring it throughout is not told to insulate first.
        $reasonable = json_decode($this->levelNamed('floor-insulation', 'Redelijke isolatie')->configurations, true);
        $this->assertSame(3, $reasonable['insulation_factor']);

        // Slecht as Geen, so it is told exactly that.
        $poor = json_decode($this->levelNamed('floor-insulation', 'Slechte isolatie')->configurations, true);
        $this->assertSame(1, $poor['insulation_factor']);
    }

    public function test_a_database_that_already_had_reasonable_gets_only_what_it_misses(): void
    {
        // Staging. Wall and roof are already where they should be; only the floor moves.
        $this->rewind(self::TO_STAGING);

        $this->artisan(self::COMMAND)->assertSuccessful();

        foreach (self::TARGET as $short => $target) {
            $this->assertSame($target, $this->stateOf($short), $short);
        }
    }

    public function test_an_answer_keeps_pointing_at_the_option_it_pointed_at(): void
    {
        // Answers reference element_value_id, so renumbering must not move them. "Matige isolatie"
        // changes from calculate value 3 to 4; the row itself, and the answer, stay put.
        $this->rewind(self::TO_ORIGINAL);

        $moderate = $this->levelNamed('floor-insulation', 'Matige isolatie (tot 8 cm isolatie)');

        $this->artisan(self::COMMAND)->assertSuccessful();

        $stillThere = DB::table('element_values')->where('id', $moderate->id)->first();

        $this->assertNotNull($stillThere);
        $this->assertSame(4, (int) $stillThere->calculate_value);
    }

    public function test_it_ends_up_where_the_seeder_would_have_put_it(): void
    {
        // A fresh install gets this state from the seeder alone. If the two ever drift apart, the
        // seeder starts inserting duplicates next to what the command produced.
        $seeded = $this->allStates();

        $this->rewind(self::TO_ORIGINAL);
        $this->artisan(self::COMMAND)->assertSuccessful();

        $this->assertSame($seeded, $this->allStates());
    }

    public function test_running_it_again_changes_nothing(): void
    {
        $this->rewind(self::TO_ORIGINAL);
        $this->artisan(self::COMMAND)->assertSuccessful();

        $after = $this->allStates();

        $this->artisan(self::COMMAND)->expectsOutputToContain('already done')->assertSuccessful();

        $this->assertSame($after, $this->allStates());
    }

    public function test_a_changed_factor_is_corrected_on_a_rerun(): void
    {
        // What makes "edit the constant, run the command" enough for a figure the stakeholders may
        // revisit, rather than hand-written SQL on production.
        $this->rewind(self::TO_ORIGINAL);
        $this->artisan(self::COMMAND)->assertSuccessful();

        $poor = $this->levelNamed('floor-insulation', 'Slechte isolatie');
        DB::table('element_values')->where('id', $poor->id)
            ->update(['configurations' => json_encode(['comfort' => 5, 'insulation_factor' => 4])]);

        $this->artisan(self::COMMAND)->assertSuccessful();

        $this->assertSame(
            ['comfort' => 0, 'insulation_factor' => 1],
            json_decode($this->levelNamed('floor-insulation', 'Slechte isolatie')->configurations, true),
        );
    }

    public function test_a_dry_run_writes_nothing(): void
    {
        $this->rewind(self::TO_ORIGINAL);

        $before = $this->allStates();

        $this->artisan(self::COMMAND . ' --dry-run')->assertSuccessful();

        $this->assertSame($before, $this->allStates());
    }

    public function test_an_unrecognised_scale_stops_the_others_from_being_written_too(): void
    {
        // Roof is inspected last, so wall and floor are already known to be fine by the time it
        // fails. Nothing may be written regardless.
        $this->rewind(self::TO_ORIGINAL);

        DB::table('element_values')
            ->where('element_id', Element::findByShort('roof-insulation')->id)
            ->where('calculate_value', 3)
            ->delete();

        $before = $this->allStates();

        $this->artisan(self::COMMAND)->assertFailed();

        $this->assertSame($before, $this->allStates());
        $this->assertNull($this->levelNamed('wall-insulation', 'Redelijke isolatie'));
        $this->assertNull($this->levelNamed('floor-insulation', 'Slechte isolatie'));
    }
}
