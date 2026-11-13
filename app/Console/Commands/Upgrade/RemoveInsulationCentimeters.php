<?php

namespace App\Console\Commands\Upgrade;

use App\Models\Element;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Takes the centimetres out of the insulation options of wall, floor and roof, on an existing
 * database: "Matige isolatie (tot 8 cm isolatie)" becomes "Matige isolatie", and so on. The
 * explanation stays in the help text of the questions. Asked by Econobis, for every scan
 * (ticket HD-1). A fresh install gets the new names from ElementsValuesTableSeeder.
 *
 * Why a command: deploy:run does not run ElementsValuesTableSeeder (see AddSmartTwinGlazingOptions).
 *
 * Rows are matched on element and their old Dutch name, not on calculate value: that depends on
 * whether upgrade:extend-insulation-scales has run. A name someone changed since is left alone, as
 * are the other languages. Safe to leave in place: once renamed it does nothing.
 */
class RemoveInsulationCentimeters extends Command
{
    protected $signature = 'upgrade:remove-insulation-centimeters {--dry-run}';

    protected $description = 'Take the centimetres out of the insulation options of wall, floor and roof.';

    public const ELEMENTS = ['wall-insulation', 'floor-insulation', 'roof-insulation'];

    /** Old Dutch name => new one. */
    public const NAMES = [
        'Matige isolatie (tot 8 cm isolatie)'           => 'Matige isolatie',
        'Goede isolatie (8 tot 20 cm isolatie)'         => 'Goede isolatie',
        'Zeer goede isolatie (meer dan 20 cm isolatie)' => 'Zeer goede isolatie',
    ];

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $renamed = 0;

        foreach (self::ELEMENTS as $short) {
            $element = Element::findByShort($short);

            if (! $element instanceof Element) {
                $this->error("Element '{$short}' not found, aborting without changing anything.");

                return self::FAILURE;
            }
        }

        foreach (self::ELEMENTS as $short) {
            $rows = DB::table('element_values')->where('element_id', Element::findByShort($short)->id)->get();

            foreach ($rows as $row) {
                $value = json_decode($row->value, true) ?? [];
                $old = $value['nl'] ?? null;

                if (! isset(self::NAMES[$old])) {
                    continue;
                }

                $value['nl'] = self::NAMES[$old];
                $this->info(($dryRun ? 'DRY-RUN ' : '') . "{$short}: {$old} -> {$value['nl']}");
                $renamed++;

                if (! $dryRun) {
                    DB::table('element_values')->where('id', $row->id)->update([
                        'value'      => json_encode($value),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        if ($renamed === 0) {
            $this->line('Already done.');

            return self::SUCCESS;
        }

        $this->info(($dryRun ? 'Done. Would rename ' : 'Done. Renamed ') . "{$renamed} option(s).");

        return self::SUCCESS;
    }
}
