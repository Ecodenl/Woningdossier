<?php

namespace App\Console\Commands\Api\Verbeterjehuis\Mappings;

use App\Helpers\Wrapper;
use App\Models\MeasureApplication;
use App\Services\DiscordNotifier;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\RegulationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
     * Execute the console command.
     */
    public function handle(MappingService $mappingService): int
    {
        // Only energy saving measure applications. The maintenace ones don't have subsidy.
        $map = [
            'floor-insulation' => [1503, 1555], // Woningisolatie / Vloer
            'bottom-insulation' => [1503, 1555], // Woningisolatie / Vloer
            'floor-insulation-research' => [1503, 1555], // Woningisolatie / Vloer
            'cavity-wall-insulation' => [1503, 1519], // Woningisolatie / Muur
            'facade-wall-insulation' => [1503, 1519], // Woningisolatie / Muur
            'wall-insulation-research' => [1503, 1519], // Woningisolatie / Muur
            'glass-in-lead' => [1503, 1545], // Woningisolatie / Glas
            'hrpp-glass-only' => [1503, 1545], // Woningisolatie / Glas
            'hrpp-glass-frames' => [1503, 1545], // Woningisolatie / Glas
            'hr3p-frames' => [1503, 1545], // Woningisolatie / Glas
            'crack-sealing' => [],
            'roof-insulation-pitched-inside' => [1503, 1532], // Woningisolatie / Dak
            'roof-insulation-pitched-replace-tiles' => [1503, 1532], // Woningisolatie / Dak
            'roof-insulation-flat-current' => [1503, 1532], // Woningisolatie / Dak
            'roof-insulation-flat-replace-current' => [1503, 1532], // Woningisolatie / Dak
            'high-efficiency-boiler-replace' => [],
            'heater-place-replace' => [1584], // Zonneboiler
            'solar-panels-place-replace' => [1571], // Zonnepanelen
            'ventilation-balanced-wtw' => [1581, 1600], // Ventilatie / Warmte-teruglevering uit ventilatielucht
            'ventilation-decentral-wtw' => [1581, 1600], // Ventilatie / Warmte-teruglevering uit ventilatielucht
            'ventilation-demand-driven' => [1581], // Ventilatie
            'hybrid-heat-pump-outside-air' => [1564], // Warmtepomp
            'hybrid-heat-pump-ventilation-air' => [1564, 1600], // Warmtepomp / Warmte-teruglevering uit ventilatielucht
            'hybrid-heat-pump-pvt-panels' => [1564, 1571], // Warmtepomp / Zonnepanelen
            'full-heat-pump-outside-air' => [1564], // Warmtepomp
            'full-heat-pump-ground-heat' => [1564], // Warmtepomp
            'full-heat-pump-pvt-panels' => [1564, 1571], // Warmtepomp / Zonnepanelen
            'heat-pump-boiler-place-replace' => [1564, 1600], // Warmtepomp / Warmte-teruglevering uit ventilatielucht
            'save-energy-with-light' => [1603], // Kleine energiebesparende maatregelen
            'energy-efficient-equipment' => [1603], // Kleine energiebesparende maatregelen
            'energy-efficient-installations' => [1603], // Kleine energiebesparende maatregelen
            'save-energy-with-crack-sealing' => [1603], // Kleine energiebesparende maatregelen
            'improve-radiators' => [1603], // Kleine energiebesparende maatregelen
            'improve-heating-installations' => [1603], // Kleine energiebesparende maatregelen
            'save-energy-with-warm-tap-water' => [1603], // Kleine energiebesparende maatregelen
            'general' => [1603] // Kleine energiebesparende maatregelen
        ];

        Wrapper::wrapCall(function () use ($map, $mappingService) {
            $targetGroups = collect(
                RegulationService::init()->getFilters()['Measures']
            )->keyBy('Value');

            foreach ($map as $measureApplicationShort => $targetMeasureValues) {
                $syncData = [];
                foreach ($targetMeasureValues as $targetMeasureValue) {
                    if ($targetGroups->has($targetMeasureValue)) {
                        $syncData[] = $targetGroups[$targetMeasureValue];
                    } else {
                        Log::error("SyncMeasures: Target group key $targetMeasureValue not found for $measureApplicationShort. Will not be synced.");
                    }
                }
                $mappingService
                    ->from(MeasureApplication::findByShort($measureApplicationShort))
                    ->sync($syncData);
            }

            $this->info("Measures mapped to MeasureApplication.");
            Log::debug("Measures mapped to MeasureApplication.");
        }, function ($exception) {
            $this->error('Something is going on with VerbeterJeHuis!');
        });

        return self::SUCCESS;
    }
}
