<?php

namespace App\Console\Commands\Upgrade;

use App\Models\Service;
use App\Services\SmartTwin\InstallationOptions;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Adds the heat pump types and panel orientations SmartTwin can describe to an existing database
 * (InstallationOptions; decision 2026-11-07-smarttwin-installaties-mapping, V1 and P-nieuw). A fresh
 * install gets them from ServiceValuesTableSeeder and PvPanelOrientationsTableSeeder.
 *
 * Why a command: deploy:run runs neither seeder, and should not. ServiceValuesTableSeeder rewrites
 * the label and configuration of every service value; PvPanelOrientationsTableSeeder inserts every
 * orientation again. This only adds what is missing: a heat pump type by its order, an orientation
 * by its short.
 *
 * deploy:run calls it before ToolQuestionsTableSeeder, which links the new options to their
 * questions, hidden from the scans. Safe to leave in place: once they exist it does nothing.
 */
class AddSmartTwinInstallationOptions extends Command
{
    protected $signature = 'upgrade:add-smart-twin-installation-options {--dry-run}';

    protected $description = 'Add the heat pump types and panel orientations SmartTwin can describe.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $heatPump = Service::findByShort('heat-pump');

        if (! $heatPump instanceof Service) {
            $this->error("Service 'heat-pump' not found, aborting without changing anything.");

            return self::FAILURE;
        }

        $existingOrders = DB::table('service_values')->where('service_id', $heatPump->id)->pluck('order')->all();
        $heatPumpTypes = [];

        foreach (InstallationOptions::addedHeatPumpTypes() as $order => $type) {
            if (! in_array($order, $existingOrders)) {
                $this->info(($dryRun ? 'DRY-RUN ' : '') . "heat-pump: {$type['label']}");
                $heatPumpTypes[] = [
                    'service_id' => $heatPump->id,
                    'value' => json_encode(['nl' => $type['label']]),
                    'calculate_value' => null,
                    'order' => $order,
                    'is_default' => false,
                    'configurations' => json_encode(['comfort' => $type['comfort']]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        $existingShorts = DB::table('pv_panel_orientations')->pluck('short')->all();
        $orientations = [];

        foreach (InstallationOptions::addedOrientations() as $short => $orientation) {
            if (! in_array($short, $existingShorts, true)) {
                $this->info(($dryRun ? 'DRY-RUN ' : '') . "pv_panel_orientations: {$orientation['label']}");
                $orientations[] = [
                    'name' => json_encode(['nl' => $orientation['label']]),
                    'short' => $short,
                    'order' => $orientation['order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        $count = count($heatPumpTypes) + count($orientations);

        if (0 === $count) {
            $this->line('Already done.');

            return self::SUCCESS;
        }

        if (! $dryRun) {
            DB::transaction(function () use ($heatPumpTypes, $orientations) {
                DB::table('service_values')->insert($heatPumpTypes);
                DB::table('pv_panel_orientations')->insert($orientations);
            });
        }

        $this->info(($dryRun ? 'Done. Would add ' : 'Done. Added ') . "{$count} option(s).");

        return self::SUCCESS;
    }
}
