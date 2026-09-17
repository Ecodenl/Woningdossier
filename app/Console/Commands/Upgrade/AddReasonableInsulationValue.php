<?php

namespace App\Console\Commands\Upgrade;

use App\Models\Element;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Adds "Redelijke isolatie" between "Matige" and "Goede" for wall, floor and roof insulation.
 *
 * The classification behind these options runs geen / matig / redelijk / goed / zeer goed, one
 * level more than Hoomdossier has ever had. The SmartTwin mapping classifies on Rc value and needs
 * the missing one to exist before it can answer current-wall-insulation.
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
 *     php artisan upgrade:add-reasonable-insulation-value
 *     php artisan db:seed        # if the deploy seeds at all
 *
 * Safe to leave in place once it has run: it recognises its own end state and does nothing. Safe to
 * halt on as well — it exits non-zero without writing when it finds a scale it does not recognise,
 * rather than making a mess of it.
 */
class AddReasonableInsulationValue extends Command
{
    protected $signature = 'upgrade:add-reasonable-insulation-value {--dry-run}';

    protected $description = 'Add the "Redelijke isolatie" element value to wall, floor and roof insulation.';

    /**
     * Where the new value goes. Same for all three elements: straight after "Matige isolatie".
     */
    private const NEW_ORDER = 3;
    private const NEW_CALCULATE_VALUE = 4;

    /**
     * Stakeholder decision: "Redelijk" behaves as "Goed", so it carries the same insulation factor.
     *
     * It may not be higher. The factor feeds HeatPump::insulationScore(), which is looked up in
     * key_figure_insulation_factors — a table running from 1.0 to 4.0. A score above that finds no
     * row and HeatPump::calculateAdvisedSystemRequiredPower() falls back to 140 W/m², the figure
     * for the worst insulated dwelling. No error, just a wildly oversized heat pump.
     */
    private const NEW_CONFIGURATIONS = ['comfort' => 3, 'insulation_factor' => 3];

    /**
     * What has to shift to free up the new value's place, per element.
     *
     * Rows are identified by calculate_value: the label is translatable and may have been edited,
     * while this is what the seeder keys on and what the code compares against.
     *
     * Applied in the order listed — highest first — so no two rows ever hold the same
     * calculate_value halfway through.
     *
     * @var array<string, array<int, array{from: int, order: int, calculate_value: int}>>
     */
    private const SHIFTS = [
        // Wall has a gap at order 4 where floor and roof have "Zeer goede isolatie", so its
        // "Zeer goede" keeps the order it already had and only changes calculate_value.
        'wall-insulation' => [
            ['from' => 5, 'order' => 5, 'calculate_value' => 6], // Zeer goede isolatie
            ['from' => 4, 'order' => 4, 'calculate_value' => 5], // Goede isolatie
        ],
        'floor-insulation' => [
            ['from' => 6, 'order' => 6, 'calculate_value' => 7], // Niet van toepassing
            ['from' => 5, 'order' => 5, 'calculate_value' => 6], // Zeer goede isolatie
            ['from' => 4, 'order' => 4, 'calculate_value' => 5], // Goede isolatie
        ],
        'roof-insulation' => [
            ['from' => 6, 'order' => 6, 'calculate_value' => 7],
            ['from' => 5, 'order' => 5, 'calculate_value' => 6],
            ['from' => 4, 'order' => 4, 'calculate_value' => 5],
        ],
    ];

    /**
     * The `(order, calculate_value)` pairs an element holds before and after, so a run can tell
     * "still to do" from "already done" from "something else entirely" instead of assuming.
     *
     * @var array<string, array{before: array<int, array<int, int>>, after: array<int, array<int, int>>}>
     */
    private const STATES = [
        'wall-insulation' => [
            'before' => [[0, 1], [1, 2], [2, 3], [3, 4], [5, 5]],
            'after'  => [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6]],
        ],
        'floor-insulation' => [
            'before' => [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6]],
            'after'  => [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6], [6, 7]],
        ],
        'roof-insulation' => [
            'before' => [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6]],
            'after'  => [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6], [6, 7]],
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
        // as it was, whichever of the three it is — which is what makes this safe to drop into a
        // deploy script.
        $todo = [];

        foreach (array_keys(self::SHIFTS) as $short) {
            $element = Element::findByShort($short);

            if (! $element instanceof Element) {
                $this->error("Element '{$short}' not found, aborting without changing anything.");

                return self::FAILURE;
            }

            $state = $this->currentState($element->id);

            if ($state === self::STATES[$short]['after']) {
                $this->line("{$short}: already done.");

                continue;
            }

            if ($state !== self::STATES[$short]['before']) {
                $this->error("{$short}: unexpected element values, aborting without changing anything.");
                $this->line('  found:    ' . $this->describe($state));
                $this->line('  expected: ' . $this->describe(self::STATES[$short]['before']));

                return self::FAILURE;
            }

            $todo[$short] = $element->id;
        }

        if (empty($todo)) {
            $this->info('Nothing to do.');

            return self::SUCCESS;
        }

        if ($dryRun) {
            foreach ($todo as $short => $elementId) {
                $this->info("DRY-RUN {$short}: " . count(self::SHIFTS[$short]) . ' values would shift, 1 would be added.');
            }

            $this->info('Done. ' . count($todo) . ' element(s) would change.');

            return self::SUCCESS;
        }

        DB::transaction(function () use ($todo) {
            foreach ($todo as $short => $elementId) {
                $this->applyTo($elementId, $short);
                $this->info("{$short}: 'Redelijke isolatie' added.");
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

    private function applyTo(int $elementId, string $short): void
    {
        foreach (self::SHIFTS[$short] as $shift) {
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
            'value'           => json_encode(['nl' => 'Redelijke isolatie']),
            'order'           => self::NEW_ORDER,
            'calculate_value' => self::NEW_CALCULATE_VALUE,
            'configurations'  => json_encode(self::NEW_CONFIGURATIONS),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
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
