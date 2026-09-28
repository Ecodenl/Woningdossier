<?php

namespace App\Console\Commands\Upgrade;

use App\Models\Element;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Brings the insulation scales up to the classification the advice is based on.
 *
 * That classification runs geen / slecht / matig / redelijk / goed / zeer goed. Hoomdossier never
 * had the middle two. The SmartTwin mapping classifies on Rc value and needs them to exist before
 * it can answer current-wall-insulation and current-floor-insulation.
 *
 * Not every element gets every level. "Redelijk" applies to wall, floor and roof; "Slecht" only to
 * the floor, whose classification is the one that uses it. The roof waits for its own table.
 *
 * Why a command and not a migration: this is data, and it has to be runnable on its own around a
 * deploy rather than at whatever moment migrations happen to run.
 *
 * **Run this before seeding.** ElementsValuesTableSeeder upserts on
 * `(element_id, calculate_value, order)`. Seeding first on an existing database means the changed
 * rows no longer match their key, and the seeder inserts duplicates next to the originals instead
 * of updating them. This command puts the existing rows on their new keys so the seeder lines up
 * again; a fresh install gets the same end state from the seeder alone.
 *
 * In a deploy script:
 *
 *     php artisan migrate
 *     php artisan upgrade:extend-insulation-scales
 *     php artisan db:seed        # if the deploy seeds at all
 *
 * Safe to leave in place once it has run: it recognises its own end state and does nothing but
 * check that the levels it added still carry the configuration they should. Safe to halt on as
 * well — it exits non-zero without writing when it finds a scale it does not recognise.
 */
class ExtendInsulationScales extends Command
{
    protected $signature = 'upgrade:extend-insulation-scales {--dry-run}';

    protected $description = 'Add the missing insulation levels to the wall, floor and roof scales.';

    /**
     * Stakeholder decision: both new levels behave as the one above them, so "Redelijk" carries the
     * insulation factor of "Goed" and "Slecht" that of "Geen".
     *
     * Slecht at 1 is what makes the "improve this first" advice fire for it, which is the point: a
     * badly insulated floor should be dealt with before a heat pump is worth having.
     *
     * A factor may never exceed 4. It feeds HeatPump::insulationScore(), which is looked up in
     * key_figure_insulation_factors — a table running from 1.0 to 4.0. A score above that finds no
     * row and HeatPump::calculateAdvisedSystemRequiredPower() falls back to 140 W/m², the figure for
     * the worst insulated dwelling. No error, just a wildly oversized heat pump.
     */
    private const REASONABLE = ['nl' => 'Redelijke isolatie', 'configurations' => ['comfort' => 3, 'insulation_factor' => 3]];
    private const POOR = ['nl' => 'Slechte isolatie', 'configurations' => ['comfort' => 0, 'insulation_factor' => 1]];

    /**
     * What each element's scale should end up as, and how to get there from a state we recognise.
     *
     * A step's `from` is the `(order, calculate_value)` pairs the element holds before it runs.
     * Steps are listed in the order they were introduced, so a database that already had the first
     * one applied — staging, where Redelijk has run — is recognised at the second.
     *
     * Rows are identified by calculate_value: the label is translatable and may have been edited,
     * while this is what the seeder keys on and what the code compares against. Shifts are applied
     * highest first, so no two rows ever hold the same calculate_value halfway through.
     */
    private const SCALES = [
        'wall-insulation' => [
            'target' => [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6]],
            'added'  => [4 => self::REASONABLE],
            'steps'  => [
                [
                    'name'   => 'Redelijke isolatie',
                    'from'   => [[0, 1], [1, 2], [2, 3], [3, 4], [5, 5]],
                    // Wall has a gap at order 4 where floor and roof have "Zeer goede isolatie", so
                    // its "Zeer goede" keeps the order it already had.
                    'shifts' => [['from' => 5, 'order' => 5, 'calculate_value' => 6],
                                 ['from' => 4, 'order' => 4, 'calculate_value' => 5]],
                    'insert' => ['order' => 3, 'calculate_value' => 4, 'level' => self::REASONABLE],
                ],
            ],
        ],

        'floor-insulation' => [
            'target' => [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6], [6, 7], [7, 8]],
            'added'  => [3 => self::POOR, 5 => self::REASONABLE],
            'steps'  => [
                [
                    'name'   => 'Redelijke isolatie',
                    'from'   => [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6]],
                    'shifts' => [['from' => 6, 'order' => 6, 'calculate_value' => 7],
                                 ['from' => 5, 'order' => 5, 'calculate_value' => 6],
                                 ['from' => 4, 'order' => 4, 'calculate_value' => 5]],
                    'insert' => ['order' => 3, 'calculate_value' => 4, 'level' => self::REASONABLE],
                ],
                [
                    'name'   => 'Slechte isolatie',
                    'from'   => [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6], [6, 7]],
                    'shifts' => [['from' => 7, 'order' => 7, 'calculate_value' => 8],
                                 ['from' => 6, 'order' => 6, 'calculate_value' => 7],
                                 ['from' => 5, 'order' => 5, 'calculate_value' => 6],
                                 ['from' => 4, 'order' => 4, 'calculate_value' => 5],
                                 ['from' => 3, 'order' => 3, 'calculate_value' => 4]],
                    'insert' => ['order' => 2, 'calculate_value' => 3, 'level' => self::POOR],
                ],
            ],
        ],

        'roof-insulation' => [
            'target' => [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6], [6, 7]],
            'added'  => [4 => self::REASONABLE],
            'steps'  => [
                [
                    'name'   => 'Redelijke isolatie',
                    'from'   => [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6]],
                    'shifts' => [['from' => 6, 'order' => 6, 'calculate_value' => 7],
                                 ['from' => 5, 'order' => 5, 'calculate_value' => 6],
                                 ['from' => 4, 'order' => 4, 'calculate_value' => 5]],
                    'insert' => ['order' => 3, 'calculate_value' => 4, 'level' => self::REASONABLE],
                ],
            ],
        ],
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY-RUN mode: no changes will be made.');
        }

        // Every element is inspected before any of them is written to, and the writes then happen
        // in one transaction. A scale this does not recognise therefore leaves the database exactly
        // as it was, whichever of the three it is.
        $todo = [];

        foreach (self::SCALES as $short => $scale) {
            $element = Element::findByShort($short);

            if (! $element instanceof Element) {
                $this->error("Element '{$short}' not found, aborting without changing anything.");

                return self::FAILURE;
            }

            $state = $this->currentState($element->id);

            if ($state === $scale['target']) {
                $this->line("{$short}: already done.");
                $this->correctConfigurations($element->id, $short, $dryRun);

                continue;
            }

            $steps = $this->stepsFrom($state, $scale);

            if (is_null($steps)) {
                $this->error("{$short}: unexpected element values, aborting without changing anything.");
                $this->line('  found:    ' . $this->describe($state));
                $this->line('  expected: ' . implode('  or  ', array_map(
                    fn (array $step) => $this->describe($step['from']),
                    $scale['steps'],
                )));

                return self::FAILURE;
            }

            $todo[$short] = ['element_id' => $element->id, 'steps' => $steps];
        }

        if (empty($todo)) {
            return self::SUCCESS;
        }

        foreach ($todo as $short => $work) {
            $names = implode(' + ', array_column($work['steps'], 'name'));
            $this->info(($dryRun ? 'DRY-RUN ' : '') . "{$short}: {$names}");
        }

        if ($dryRun) {
            $this->info('Done. ' . count($todo) . ' element(s) would change.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($todo) {
            foreach ($todo as $work) {
                foreach ($work['steps'] as $step) {
                    $this->applyStep($work['element_id'], $step);
                }
            }
        });

        foreach (array_keys($todo) as $short) {
            // The element itself is cached by short. Its values are not part of that cache, but
            // clearing it keeps a stale hit from ever being the explanation for a missing option.
            // Outside the transaction: a cleared cache costs a query, a cleared-then-rolled-back
            // one costs nothing at all.
            Element::clearShortCache($short);
        }

        $this->info('Done. ' . count($todo) . ' element(s) changed.');

        return self::SUCCESS;
    }

    /**
     * The steps still to run, or null when the state is not one this knows how to move on from.
     *
     * @param  array<int, array<int, int>>  $state
     * @param  array<string, mixed>         $scale
     * @return null|array<int, array<string, mixed>>
     */
    private function stepsFrom(array $state, array $scale): ?array
    {
        foreach ($scale['steps'] as $index => $step) {
            if ($state === $step['from']) {
                return array_slice($scale['steps'], $index);
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $step
     */
    private function applyStep(int $elementId, array $step): void
    {
        foreach ($step['shifts'] as $shift) {
            DB::table('element_values')
                ->where('element_id', $elementId)
                ->where('calculate_value', $shift['from'])
                ->update([
                    'order'           => $shift['order'],
                    'calculate_value' => $shift['calculate_value'],
                    'updated_at'      => now(),
                ]);
        }

        DB::table('element_values')->insert([
            'element_id'      => $elementId,
            'value'           => json_encode(['nl' => $step['insert']['level']['nl']]),
            'order'           => $step['insert']['order'],
            'calculate_value' => $step['insert']['calculate_value'],
            'configurations'  => json_encode($step['insert']['level']['configurations']),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    /**
     * Bring the levels this command added back in line with what it says they should be.
     *
     * Without this, changing a factor above would do nothing on a database where the command has
     * already run: it would see its own end state and stop. This is what makes "edit the constant,
     * run the command" enough, rather than hand-written SQL on production.
     *
     * Note that a change only reaches an existing advice once its step is recalculated.
     */
    private function correctConfigurations(int $elementId, string $short, bool $dryRun): void
    {
        foreach (self::SCALES[$short]['added'] as $calculateValue => $level) {
            $row = DB::table('element_values')
                ->where('element_id', $elementId)
                ->where('calculate_value', $calculateValue)
                ->first();

            if (is_null($row) || json_decode($row->configurations, true) === $level['configurations']) {
                continue;
            }

            $this->warn(sprintf(
                '  %s: configuration %s -> %s%s',
                $level['nl'],
                $row->configurations,
                json_encode($level['configurations']),
                $dryRun ? ' (DRY-RUN)' : '',
            ));

            if (! $dryRun) {
                DB::table('element_values')
                    ->where('id', $row->id)
                    ->update([
                        'configurations' => json_encode($level['configurations']),
                        'updated_at'     => now(),
                    ]);
            }
        }
    }

    /**
     * @return array<int, array<int, int>>
     */
    private function currentState(int $elementId): array
    {
        return DB::table('element_values')
            ->where('element_id', $elementId)
            ->orderBy('order')
            ->get(['order', 'calculate_value'])
            ->map(fn ($row) => [(int) $row->order, (int) $row->calculate_value])
            ->all();
    }

    /**
     * @param  array<int, array<int, int>>  $state
     */
    private function describe(array $state): string
    {
        return implode(' ', array_map(fn (array $pair) => "order={$pair[0]}/calc={$pair[1]}", $state));
    }
}
