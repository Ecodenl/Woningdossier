<?php

namespace App\Console\Commands\Reusables;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\DocBlock\Tag;

class UpdateModelValues extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reusables:update-model-values';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates model values for the (currently hardcoded) given values';

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
        $table = 'measure_applications';

        $updateData = [
            "floor-insulation" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 43,
                ]
            ],
            "bottom-insulation" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 25,
                ]
            ],
            "floor-insulation-research" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 25,
                ]
            ],
            "cavity-wall-insulation" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 29,
                ]
            ],
            "facade-wall-insulation" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 113,
                ]
            ],
            "wall-insulation-research" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 29,
                ]
            ],
            "glass-in-lead" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 188,
                ]
            ],
            "hrpp-glass-only" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 169,
                ]
            ],
            "hrpp-glass-frames" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 770,
                ]
            ],
            "hr3p-frames" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 850,
                ]
            ],
            "crack-sealing" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 533,
                ]
            ],
            "roof-insulation-pitched-inside" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 113,
                ]
            ],
            "roof-insulation-pitched-replace-tiles" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 90,
                ]
            ],
            "roof-insulation-flat-current" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 212,
                ]
            ],
            "roof-insulation-flat-replace-current" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 82,
                ]
            ],
            "high-efficiency-boiler-replace" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 2620,
                ]
            ],
            "heater-place-replace" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 3677,
                ]
            ],
            "solar-panels-place-replace" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 385,
                ]
            ],
            "repair-joint" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 70,
                ]
            ],
            "clean-brickwork" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 15,
                ]
            ],
            "impregnate-wall" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 10,
                ]
            ],
            "paint-wall" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 45,
                ]
            ],
            "paint-wood-elements" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 140,
                ]
            ],
            "replace-tiles" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 150,
                ]
            ],
            "replace-roof-insulation" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 192,
                ]
            ],
            "inspect-repair-roofs" => [
                'column' => 'short',
                'updateData' => [
                    'costs' => 250,
                ]
            ],
//            "replace-zinc-pitched" => [
//                'column' => 'short',
//                'updateData' => [
//                    'costs' => 130,
//                ]
//            ],
//            "replace-zinc-flat" => [
//                'column' => 'short',
//                'updateData' => [
//                    'costs' => 130,
//                ]
//            ],
//            "ventilation-balanced-wtw" => [
//                'column' => 'short',
//                'updateData' => [
//                    'costs' => 43,
//                ]
//            ],
//            "ventilation-decentral-wtw" => [
//                'column' => 'short',
//                'updateData' => [
//                    'costs' => 43,
//                ]
//            ],
//            "ventilation-demand-driven" => [
//                'column' => 'short',
//                'updateData' => [
//                    'costs' => 43,
//                ]
//            ],
        ];

        foreach ($updateData as $search => $updateDatum) {
            $column = $updateDatum['column'];
            DB::table($table)
                ->where($column, $search)
                ->update($updateDatum['updateData']);
        }
    }
}