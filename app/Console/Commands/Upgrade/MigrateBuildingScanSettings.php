<?php

namespace App\Console\Commands\Upgrade;

use App\Helpers\ScanAvailabilityHelper;
use App\Helpers\SmallMeasuresSettingHelper;
use App\Models\Building;
use App\Models\Scan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateBuildingScanSettings extends Command
{
    protected $signature = 'upgrade:migrate-building-scan-settings {--dry-run} {--chunk=500}';

    protected $description = 'Migrate all existing buildings to explicit building_settings records based on their cooperation\'s scan configuration.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $chunkSize = (int) $this->option('chunk') ?: 500;

        $this->info('Starting building scan settings migration...');

        if ($dryRun) {
            $this->info('DRY-RUN mode: no changes will be made.');
        }

        $now = now()->toDateTimeString();
        $totalBuildings = 0;
        $totalRows = 0;

        $simpleScans = Scan::simpleScans()->get();

        Building::query()
            ->whereNotNull('user_id')
            ->whereHas('user', fn ($q) => $q->whereNotNull('cooperation_id'))
            ->whereHas('user.cooperation', fn ($q) => $q->whereNull('deleted_at'))
            ->with('user.cooperation.scans')
            ->chunkById($chunkSize, function ($buildings) use ($dryRun, $now, $simpleScans, &$totalBuildings, &$totalRows) {
                $rows = [];

                foreach ($buildings as $building) {
                    if (! $building->user?->cooperation) {
                        $this->warn("Building {$building->id} skipped: missing user or cooperation.");
                        continue;
                    }

                    $totalBuildings++;

                    foreach ($simpleScans as $scan) {
                        $cooperationScan = $building->user->cooperation->scans->firstWhere('id', $scan->id);
                        $scanEnabled = $cooperationScan !== null;

                        $rows[] = [
                            'building_id' => $building->id,
                            'short' => ScanAvailabilityHelper::getBuildingSettingShort($scan),
                            'value' => $scanEnabled ? '1' : '0',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];

                        $smallMeasuresEnabled = match (true) {
                            $scan->isLiteScan() => true,
                            $cooperationScan !== null => (bool) ($cooperationScan->pivot->small_measures_enabled ?? true),
                            default => true,
                        };

                        $rows[] = [
                            'building_id' => $building->id,
                            'short' => SmallMeasuresSettingHelper::getBuildingSettingShort($scan),
                            'value' => $smallMeasuresEnabled ? '1' : '0',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                    }
                }

                if (empty($rows)) {
                    return;
                }

                $rowCount = count($rows);

                if ($dryRun) {
                    $this->info("DRY-RUN: {$buildings->count()} buildings, {$rowCount} rows would be inserted (actual count may be lower due to existing overrides).");
                    $totalRows += $rowCount;
                    return;
                }

                DB::transaction(function () use ($rows, $buildings, &$totalRows) {
                    $inserted = DB::table('building_settings')->insertOrIgnore($rows);
                    $totalRows += $inserted;
                    $this->info("Chunk processed: {$buildings->count()} buildings, {$inserted} rows inserted.");
                });
            });

        $this->info("Done. Total: {$totalBuildings} buildings processed, {$totalRows} rows " . ($dryRun ? 'would be inserted.' : 'inserted.'));

        return self::SUCCESS;
    }
}
