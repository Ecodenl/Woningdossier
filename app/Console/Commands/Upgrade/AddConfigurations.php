<?php

namespace App\Console\Commands\Upgrade;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddConfigurations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:add-configurations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Adds new configurations to various tables';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $tables = [
            'measure_applications' => [
                [
                    'wheres' => [
                        'short' => 'floor-insulation',
                    ],
                    'configurations' => [
                        'comfort' => 5,
                        'icon' => 'icon-floor-insulation-excellent',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'bottom-insulation',
                    ],
                    'configurations' => [
                        'comfort' => 3,
                        'icon' => 'icon-floor-insulation-good',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'floor-insulation-research',
                    ],
                    'configurations' => [
                        'comfort' => 2,
                        'icon' => 'icon-floor-insulation-moderate',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'cavity-wall-insulation',
                    ],
                    'configurations' => [
                        'comfort' => 3,
                        'icon' => 'icon-wall-insulation-good',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'facade-wall-insulation',
                    ],
                    'configurations' => [
                        'comfort' => 5,
                        'icon' => 'icon-wall-insulation-excellent',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'wall-insulation-research',
                    ],
                    'configurations' => [
                        'comfort' => 3,
                        'icon' => 'icon-wall-insulation-moderate',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'glass-in-lead',
                    ],
                    'configurations' => [
                        'comfort' => 3,
                        'icon' => 'icon-glass-single',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'hrpp-glass-only',
                    ],
                    'configurations' => [
                        'comfort' => 4,
                        'icon' => 'icon-glass-hr-p',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'hrpp-glass-frames',
                    ],
                    'configurations' => [
                        'comfort' => 4,
                        'icon' => 'icon-glass-hr-dp',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'hr3p-frames',
                    ],
                    'configurations' => [
                        'comfort' => 5,
                        'icon' => 'icon-glass-hr-tp',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'crack-sealing',
                    ],
                    'configurations' => [
                        'comfort' => 3,
                        'icon' => 'icon-cracks-seams',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'roof-insulation-pitched-inside',
                    ],
                    'configurations' => [
                        'comfort' => 5,
                        'icon' => 'icon-pitched-roof',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'roof-insulation-pitched-replace-tiles',
                    ],
                    'configurations' => [
                        'comfort' => 5,
                        'icon' => 'icon-pitched-roof',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'roof-insulation-flat-current',
                    ],
                    'configurations' => [
                        'comfort' => 3,
                        'icon' => 'icon-flat-roof',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'roof-insulation-flat-replace-current',
                    ],
                    'configurations' => [
                        'comfort' => 5,
                        'icon' => 'icon-flat-roof',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'high-efficiency-boiler-replace',
                    ],
                    'configurations' => [
                        'comfort' => 3,
                        'icon' => 'icon-central-heater',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'heater-place-replace',
                    ],
                    'configurations' => [
                        'comfort' => 3,
                        'icon' => 'icon-sun-boiler',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'solar-panels-place-replace',
                    ],
                    'configurations' => [
                        'comfort' => 1,
                        'icon' => 'icon-solar-panels',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'repair-joint',
                    ],
                    'configurations' => [
                        'comfort' => 0,
                        'icon' => 'icon-tools',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'clean-brickwork',
                    ],
                    'configurations' => [
                        'comfort' => 0,
                        'icon' => 'icon-tools',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'impregnate-wall',
                    ],
                    'configurations' => [
                        'comfort' => 0,
                        'icon' => 'icon-hydronic-balance-temperature',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'paint-wall',
                    ],
                    'configurations' => [
                        'comfort' => 0,
                        'icon' => 'icon-paint-job',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'paint-wood-elements',
                    ],
                    'configurations' => [
                        'comfort' => 0,
                        'icon' => 'icon-paint-job',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'replace-tiles',
                    ],
                    'configurations' => [
                        'comfort' => 0,
                        'icon' => 'icon-tools',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'replace-roof-insulation',
                    ],
                    'configurations' => [
                        'comfort' => 0,
                        'icon' => 'icon-roof-insulation-excellent',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'inspect-repair-roofs',
                    ],
                    'configurations' => [
                        'comfort' => 0,
                        'icon' => 'icon-tools',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'replace-zinc-pitched',
                    ],
                    'configurations' => [
                        'comfort' => 0,
                        'icon' => 'icon-pitched-roof',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'replace-zinc-flat',
                    ],
                    'configurations' => [
                        'comfort' => 0,
                        'icon' => 'icon-flat-roof',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'ventilation-balanced-wtw',
                    ],
                    'configurations' => [
                        'comfort' => 5,
                        'icon' => 'icon-ventilation',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'ventilation-decentral-wtw',
                    ],
                    'configurations' => [
                        'comfort' => 5,
                        'icon' => 'icon-ventilation',
                    ],
                ],
                [
                    'wheres' => [
                        'short' => 'ventilation-demand-driven',
                    ],
                    'configurations' => [
                        'comfort' => 4,
                        'icon' => 'icon-ventilation',
                    ],
                ],
            ],
            'element_values' => [
                [
                    'wheres' => [
                        'value->nl' => 'Enkelglas',
                    ],
                    'configurations' => [
                        'comfort' => 0,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Dubbelglas',
                    ],
                    'configurations' => [
                        'comfort' => 3,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'HR++ glas',
                    ],
                    'configurations' => [
                        'comfort' => 5,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Drievoudige beglazing',
                    ],
                    'configurations' => [
                        'comfort' => 5,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Onbekend',
                    ],
                    'configurations' => [
                        'comfort' => 0,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Geen isolatie',
                    ],
                    'configurations' => [
                        'comfort' => 0,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Matige isolatie (tot 8 cm isolatie)',
                    ],
                    'configurations' => [
                        'comfort' => 3,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Goede isolatie (8 tot 20 cm isolatie)',
                    ],
                    'configurations' => [
                        'comfort' => 3,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Zeer goede isolatie (meer dan 20 cm isolatie)',
                    ],
                    'configurations' => [
                        'comfort' => 5,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Niet van toepassing',
                    ],
                    'configurations' => [
                        'comfort' => 0,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Ja, in goede staat', // Kierdichting
                    ],
                    'configurations' => [
                        'comfort' => 3,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Ja, in slechte staat', // Kierdichting
                    ],
                    'configurations' => [
                        'comfort' => 1,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Nee', // Kierdichting
                    ],
                    'configurations' => [
                        'comfort' => 0,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Alleen houten kozijnen', // Kozijnen
                    ],
                    'configurations' => [
                        'comfort' => 0, // No relevance
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Houten kozijnen en enkele andere kozijnen (bijvoorbeeld kunststof of aluminium)', // Kozijnen
                    ],
                    'configurations' => [
                        'comfort' => 0, // No relevance
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Enkele houten kozijnen, voornamelijk kunststof en of aluminium', // Kozijnen
                    ],
                    'configurations' => [
                        'comfort' => 0, // No relevance
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Geen houten kozijnen', // Kozijnen
                    ],
                    'configurations' => [
                        'comfort' => 0, // No relevance
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Overig', // Kozijnen
                    ],
                    'configurations' => [
                        'comfort' => 0, // No relevance
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Dakranden / boeidelen', // Houten bouwdelen
                    ],
                    'configurations' => [
                        'comfort' => 0, // No relevance
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Dakkapellen', // Houten bouwdelen
                    ],
                    'configurations' => [
                        'comfort' => 0, // No relevance
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Gevelbekleding', // Houten bouwdelen
                    ],
                    'configurations' => [
                        'comfort' => 0, // No relevance
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Best hoog (meer dan 45 cm)', // Kruipruimte
                    ],
                    'configurations' => [
                        'comfort' => 0, // No relevance
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Laag (tussen 30 en 45 cm)', // Kruipruimte
                    ],
                    'configurations' => [
                        'comfort' => 0, // No relevance
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Heel laag (minder dan 30 cm)', // Kruipruimte
                    ],
                    'configurations' => [
                        'comfort' => 0, // No relevance
                    ],
                ],
            ],
            'service_values' => [
                [
                    'wheres' => [
                        'value->nl' => 'Geen', // Zonneboiler
                    ],
                    'configurations' => [
                        'comfort' => 0,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Voor warm tapwater', // Zonneboiler
                    ],
                    'configurations' => [
                        'comfort' => 3,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Voor verwarming', // Zonneboiler
                    ],
                    'configurations' => [
                        'comfort' => 1,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Voor verwarming en warm tapwater', // Zonneboiler
                    ],
                    'configurations' => [
                        'comfort' => 3,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Aanwezig, recent vervangen', // HR CV Ketel
                    ],
                    'configurations' => [
                        'comfort' => 0, // Removed
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Aanwezig, tussen 6 en 13 jaar oud', // HR CV Ketel
                    ],
                    'configurations' => [
                        'comfort' => 0, // Removed
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Aanwezig, ouder dan 13 jaar', // HR CV Ketel
                    ],
                    'configurations' => [
                        'comfort' => 0, // Removed
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Niet aanwezig', // HR CV Ketel
                    ],
                    'configurations' => [
                        'comfort' => 0, // Removed
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Onbekend', // HR CV Ketel
                    ],
                    'configurations' => [
                        'comfort' => 0, // Removed
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'conventioneel rendement ketel', // Type ketel
                    ],
                    'configurations' => [
                        'comfort' => 0,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'verbeterd rendement ketel', // Type ketel
                    ],
                    'configurations' => [
                        'comfort' => 0,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'HR100 ketel', // Type ketel
                    ],
                    'configurations' => [
                        'comfort' => 1,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'HR104 ketel', // Type ketel
                    ],
                    'configurations' => [
                        'comfort' => 2,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'HR107 ketel', // Type ketel
                    ],
                    'configurations' => [
                        'comfort' => 3,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Natuurlijke ventilatie', // Hoe wordt het huis geventileerd
                    ],
                    'configurations' => [
                        'comfort' => 0,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Mechanische ventilatie', // Hoe wordt het huis geventileerd
                    ],
                    'configurations' => [
                        'comfort' => 1,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Gebalanceerde ventilatie', // Hoe wordt het huis geventileerd
                    ],
                    'configurations' => [
                        'comfort' => 3,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Decentrale mechanische ventilatie', // Hoe wordt het huis geventileerd
                    ],
                    'configurations' => [
                        'comfort' => 3,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Geen', // Warmtepomp
                    ],
                    'configurations' => [
                        'comfort' => 0,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Volledige warmtepomp buitenlucht', // Warmtepomp
                    ],
                    'configurations' => [
                        'comfort' => 3,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Volledige warmtepomp bodem', // Warmtepomp
                    ],
                    'configurations' => [
                        'comfort' => 3,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Hybride warmtepomp', // Warmtepomp
                    ],
                    'configurations' => [
                        'comfort' => 2,
                    ],
                ],
                [
                    'wheres' => [
                        'value->nl' => 'Collectieve warmtepomp', // Warmtepomp
                    ],
                    'configurations' => [
                        'comfort' => 2,
                    ],
                ],
            ],
        ];

        foreach ($tables as $table => $data) {
            foreach ($data as $configurationInfo) {
                $where = $configurationInfo['wheres'];
                $config = $configurationInfo['configurations'];
                DB::table($table)
                    ->where($where)
                    ->update(['configurations' => json_encode($config)]);
            }
        }
    }
}
