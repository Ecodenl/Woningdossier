<?php

namespace App\Console\Commands\Api\SmartTwin;

use App\Helpers\Hoomdossier;
use App\Models\SmartTwinSolution;
use App\Services\SmartTwin\Api\SmartTwinApi;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Imports SmartTwin's solution catalogue so the coupling screen has something to list.
 *
 * Import only: which measure application a solution is stays with whoever uses that screen. A
 * command deciding that too would silently undo their work on every run, and the catalogue grows
 * without the API changing, so it has to be re-runnable.
 *
 * Products are never deleted. An advice that came in last year may still name one, and its coupling
 * has to keep resolving; a product the catalogue no longer holds is reported instead, and the screen
 * marks it.
 */
class ImportSolutions extends Command
{
    protected $signature = 'api:smarttwin:import-solutions {--dry-run}';

    protected $description = 'Import the SmartTwin solution catalogue.';

    public function handle(SmartTwinApi $api): int
    {
        if (! Hoomdossier::hasEnabledSmartTwinCalls()) {
            $this->error('SmartTwin calls are disabled, set SMARTTWIN_ENABLED and SMARTTWIN_KEY.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY-RUN mode: no changes will be made.');
        }

        $solutions = $api->advice()->getAllSolutions()['solutions'] ?? [];

        if (empty($solutions)) {
            // A catalogue that comes back empty is a failing call far more often than a withdrawn
            // catalogue, and acting on it would mark every product as gone.
            $this->error('The catalogue came back empty, aborting without changing anything.');

            return self::FAILURE;
        }

        $rows = $this->rows($solutions);

        if (count($rows) !== count($solutions)) {
            $this->warn(sprintf(
                '%d solution(s) share an id or carry none; %d entries in the catalogue, %d distinct ids.',
                count($solutions) - count($rows),
                count($solutions),
                count($rows),
            ));
        }

        $known = SmartTwinSolution::pluck('name', 'external_id');
        $new = array_diff_key($rows, $known->all());
        $renamed = array_filter(
            array_intersect_key($rows, $known->all()),
            fn (array $row, string $id) => $row['name'] !== $known[$id],
            ARRAY_FILTER_USE_BOTH,
        );
        $gone = array_diff_key($known->all(), $rows);

        $this->report('New', array_map(fn (array $row) => $row['name'], $new));
        $this->report('Renamed', array_map(fn (array $row) => $row['name'], $renamed));
        $this->report('No longer in the catalogue', $gone);

        if (! $dryRun) {
            $this->store($rows);
        }

        $this->newLine();
        $this->info(sprintf(
            '%d solution(s) in the catalogue: %d new, %d renamed, %d no longer offered%s.',
            count($rows),
            count($new),
            count($renamed),
            count($gone),
            $dryRun ? ' (nothing written)' : '',
        ));

        if (! empty($gone)) {
            $this->warn('Withdrawn products are kept: an advice from before the withdrawal still names them.');
        }

        return self::SUCCESS;
    }

    /**
     * @param  array<int, array<string, mixed>>  $solutions
     * @return array<string, array<string, mixed>>  keyed on the solution id
     */
    private function rows(array $solutions): array
    {
        $rows = [];

        foreach ($solutions as $solution) {
            $externalId = trim((string) ($solution['id'] ?? ''));

            if ('' === $externalId) {
                continue;
            }

            $rows[$externalId] = [
                'external_id' => $externalId,
                'name'        => (string) ($solution['name'] ?? $externalId),
                'kind'        => SmartTwinSolution::kindOf($externalId),
            ];
        }

        return $rows;
    }

    /**
     * @param  array<string, array<string, mixed>>  $rows
     */
    private function store(array $rows): void
    {
        // One timestamp for the whole import, so "not seen by the last import" is a single
        // comparison rather than a window of a few seconds. See SmartTwinSolution::withdrawn().
        $seenAt = Carbon::now();
        $stamps = ['last_seen_at' => $seenAt, 'created_at' => $seenAt, 'updated_at' => $seenAt];

        DB::transaction(function () use ($rows, $stamps) {
            foreach (array_chunk($rows, 100) as $chunk) {
                SmartTwinSolution::upsert(
                    array_map(fn (array $row) => $row + $stamps, $chunk),
                    ['external_id'],
                    ['name', 'kind', 'last_seen_at', 'updated_at'],
                );
            }
        });
    }

    /**
     * @param  array<string, string>  $names  keyed on the solution id
     */
    private function report(string $heading, array $names): void
    {
        if (empty($names)) {
            return;
        }

        $this->newLine();
        $this->line("<info>{$heading}</info> (" . count($names) . ')');

        ksort($names);

        foreach ($names as $externalId => $name) {
            $this->line("   {$name}");
            $this->line("     <fg=gray>{$externalId}</>");
        }
    }
}
