<?php

namespace App\Console\Commands\Upgrade;

use App\Enums\GlassType;
use App\Models\Element;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Adds the glass and frame options SmartTwin tells apart to an existing database: HR, HR+ and vacuum
 * glass, and the four frame types (decision 2026-11-07-smarttwin-glasmapping). A fresh install gets
 * them from ElementsValuesTableSeeder, with the same values as here.
 *
 * Why a command: deploy:run does not run ElementsValuesTableSeeder, and should not. That seeder
 * writes the label and configuration of every option, and on a database where
 * upgrade:extend-insulation-scales has not run it inserts duplicates. This only adds what is
 * missing, matched on element and order, and leaves every existing row alone.
 *
 * deploy:run calls it before ToolQuestionsTableSeeder, which links the new options to the glass and
 * frame questions, hidden from the scans. Safe to leave in place: once the options exist it does
 * nothing.
 */
class AddSmartTwinGlazingOptions extends Command
{
    protected $signature = 'upgrade:add-smart-twin-glazing-options {--dry-run}';

    protected $description = 'Add the glass and frame options SmartTwin tells apart.';

    /**
     * By element: order => label, calculate value and configuration, as in ElementsValuesTableSeeder.
     */
    private const OPTIONS = [
        'living-rooms-windows' => self::GLASS,
        'sleeping-rooms-windows' => self::GLASS,
        'frames' => [
            5 => ['nl' => 'Hout', 'calculate_value' => 1, 'configurations' => ['comfort' => 0]],
            6 => ['nl' => 'Kunststof', 'calculate_value' => 0, 'configurations' => ['comfort' => 0]],
            7 => ['nl' => 'Aluminium thermisch gescheiden', 'calculate_value' => 0, 'configurations' => ['comfort' => 0]],
            8 => ['nl' => 'Aluminium niet thermisch gescheiden', 'calculate_value' => 0, 'configurations' => ['comfort' => 0]],
        ],
    ];

    private const GLASS = [
        GlassType::HR->value => [
            'nl' => 'HR glas', 'calculate_value' => null, 'configurations' => ['comfort' => 3, 'insulation_factor' => 2],
        ],
        GlassType::HR_PLUS->value => [
            'nl' => 'HR+ glas', 'calculate_value' => null, 'configurations' => ['comfort' => 4, 'insulation_factor' => 2],
        ],
        GlassType::VACUUM->value => [
            'nl' => 'Vacuümglas', 'calculate_value' => null, 'configurations' => ['comfort' => 5, 'insulation_factor' => 4],
        ],
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $rows = [];

        foreach (self::OPTIONS as $short => $options) {
            $element = Element::findByShort($short);

            if (! $element instanceof Element) {
                $this->error("Element '{$short}' not found, aborting without changing anything.");

                return self::FAILURE;
            }

            $existing = DB::table('element_values')->where('element_id', $element->id)->pluck('order')->all();

            foreach ($options as $order => $option) {
                if (in_array($order, $existing)) {
                    continue;
                }

                $this->info(($dryRun ? 'DRY-RUN ' : '') . "{$short}: {$option['nl']}");
                $rows[] = [
                    'element_id' => $element->id,
                    'value' => json_encode(['nl' => $option['nl']]),
                    'calculate_value' => $option['calculate_value'],
                    'order' => $order,
                    'configurations' => json_encode($option['configurations']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if (empty($rows)) {
            $this->line('Already done.');

            return self::SUCCESS;
        }

        if (! $dryRun) {
            DB::table('element_values')->insert($rows);
        }

        $this->info(($dryRun ? 'Done. Would add ' : 'Done. Added ') . count($rows) . ' option(s).');

        return self::SUCCESS;
    }
}
