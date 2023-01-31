<?php

namespace App\Console\Commands\Api\Verbeterjehuis\Mappings;

use App\Models\Mapping;
use App\Models\MeasureApplication;
use App\Models\ToolQuestion;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\Client;
use App\Services\Verbeterjehuis\RegulationService;
use App\Services\Verbeterjehuis\Verbeterjehuis;
use Illuminate\Console\Command;

class SyncMeasures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:verbeterjehuis:mapping:sync-measures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command will map our MeasureApplications to the correct target groups.';

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
     * @return int
     */
    public function handle(MappingService $mappingService)
    {
        $map = [
            'floor-insulation' => [2934],
            'bottom-insulation' => [2934],
            'floor-insulation-research' => [2934],
            'cavity-wall-insulation' => [2933],
            'facade-wall-insulation' => [2933],
            'wall-insulation-research' => [2933],
            'glass-in-lead' => [2936],
            'hrpp-glass-only' => [2936],
            'hrpp-glass-frames' => [2936],
            'hr3p-frames' => [2936],
            'crack-sealing' => [],
            'roof-insulation-pitched-inside' => [2935],
            'roof-insulation-pitched-replace-tiles' => [2935],
            'roof-insulation-flat-current' => [2935],
            'roof-insulation-flat-replace-current' => [2935],
            'high-efficiency-boiler-replace' => [],
            'heater-place-replace' => [2837],
            'solar-panels-place-replace' => [2836],
            'ventilation-balanced-wtw' => [],
            'ventilation-decentral-wtw' => [],
            'ventilation-demand-driven' => [],
            'hybrid-heat-pump-outside-air' => [2835],
            'hybrid-heat-pump-ventilation-air' => [2835],
            'hybrid-heat-pump-pvt-panels' => [2835],
            'full-heat-pump-outside-air' => [2835],
            'full-heat-pump-ground-heat' => [2835],
            'full-heat-pump-pvt-panels' => [2835],
            'heat-pump-boiler-place-replace' => [],
            'save-energy-with-light' => [],
            'energy-efficient-equipment' => [],
            'energy-efficient-installations' => [],
            'save-energy-with-crack-sealing' => [],
            'improve-radiators' => [],
            'improve-heating-installations' => [],
            'save-energy-with-warm-tap-water' => [],
            'general' => [],
        ];

        $targetGroups = collect(
            RegulationService::init()->getFilters()['Measures']
        )->keyBy('Value');

        foreach ($map as $measureApplicationShort => $targetMeasureValues) {
            foreach ($targetMeasureValues as $targetMeasureValue) {
                $mappingService
                    ->from(MeasureApplication::findByShort($measureApplicationShort))
                    ->target($targetGroups[$targetMeasureValue])
                    ->sync();
            }
        }

        $this->info("Measures mapped to MeasureApplication.");
        return 0;
    }
}
