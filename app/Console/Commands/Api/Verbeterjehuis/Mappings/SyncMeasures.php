<?php

namespace App\Console\Commands\Api\Verbeterjehuis\Mappings;

use App\Helpers\Wrapper;
use App\Models\MeasureApplication;
use App\Services\DiscordNotifier;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\RegulationService;
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
            'floor-insulation' => [2834, 2934],
            'bottom-insulation' => [2834, 2934],
            'floor-insulation-research' => [2834, 2934],
            'cavity-wall-insulation' => [2834, 2933],
            'facade-wall-insulation' => [2834, 2933],
            'wall-insulation-research' => [2834, 2933],
            'glass-in-lead' => [2834, 2936],
            'hrpp-glass-only' => [2834, 2936],
            'hrpp-glass-frames' => [2834, 2936],
            'hr3p-frames' => [2834, 2936],
            'crack-sealing' => [], // TODO: Nothing in the mapping, but seeing we have a small-measures crack sealing, could this not apply for 4483 also? Or otherwise 4655 (overige energie maatregelen)?
            'roof-insulation-pitched-inside' => [2834, 2935],
            'roof-insulation-pitched-replace-tiles' => [2834, 2935],
            'roof-insulation-flat-current' => [2834, 2935],
            'roof-insulation-flat-replace-current' => [2834, 2935],
            'high-efficiency-boiler-replace' => [],
            'heater-place-replace' => [2837],
            'solar-panels-place-replace' => [2836],
            'ventilation-balanced-wtw' => [2937, 2941],
            'ventilation-decentral-wtw' => [2937, 2941],
            'ventilation-demand-driven' => [2937],
            'hybrid-heat-pump-outside-air' => [2835],
            'hybrid-heat-pump-ventilation-air' => [2835, 2941],
            'hybrid-heat-pump-pvt-panels' => [2835, 2836],
            'full-heat-pump-outside-air' => [2835],
            'full-heat-pump-ground-heat' => [2835],
            'full-heat-pump-pvt-panels' => [2835, 2836],
            'heat-pump-boiler-place-replace' => [2941],
            'save-energy-with-light' => [4483],
            'energy-efficient-equipment' => [4483],
            'energy-efficient-installations' => [4483],
            'save-energy-with-crack-sealing' => [4483],
            'improve-radiators' => [4483],
            'improve-heating-installations' => [4483],
            'save-energy-with-warm-tap-water' => [4483],
            'general' => [4483],
        ];

        Wrapper::wrapCall(function () use ($map, $mappingService) {
            $results = RegulationService::init()->getFilters();
            $targetGroups = collect(
                $results['Measures']
            )->keyBy('Value');

            foreach ($map as $measureApplicationShort => $targetMeasureValues) {
                $syncData = [];
                foreach ($targetMeasureValues as $targetMeasureValue) {
                    $syncData[] = $targetGroups[$targetMeasureValue];
                }
                $mappingService
                    ->from(MeasureApplication::findByShort($measureApplicationShort))
                    ->sync($syncData);
            }

            $this->info("Measures mapped to MeasureApplication.");
            DiscordNotifier::init()->notify('SyncMeasures just ran!');
        }, function () {
            $this->error('Something is going on with VerbeterJeHuis!');
        });

        return 0;
    }
}
