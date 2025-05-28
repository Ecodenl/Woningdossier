<?php

namespace App\Console\Commands\Upgrade;

use App\Helpers\MappingHelper;
use App\Helpers\Str;
use App\Models\Mapping;
use App\Models\MeasureCategory;
use App\Services\MappingService;
use App\Services\Verbeterjehuis\RegulationService;
use Illuminate\Console\Command;

class UpdateMeasureCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upgrade:update-measure-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the measure categories from old to new.';

    /**
     * Execute the console command.
     */
    public function handle(MappingService $mappingService): int
    {
        $movedOrChanged = [
            'woningisolatie' => 'Isolatie en glas',
            'muur' => 'Isolatie en glas',
            'dak' => 'Isolatie en glas',
            'glas' => 'Isolatie en glas',
            'vloer' => 'Isolatie en glas',
            'aardgasvrije-woning' => 'Gasaansluiting verwijderen',
            'aansluiting-op-warmtenet' => 'Warmtenet-aansluiting',
            'lage-temperatuur-verwarming' => 'warmtepomp',
            'warmte-teruglevering-uit-ventilatielucht' => 'ventilatie',
            'thuisaccu' => 'Thuisbatterij',
            'groene-daken' => 'Daken en gevels vergroenen',
            'tegels-uit-tuin-verwijderen' => 'Tuin vergroenen',
            'regenpijp-afkoppelen-van-riool' => 'Regenwater opvangen',
            'geveltuin' => 'Daken en gevels vergroenen',
            'regenwatertuin' => 'Regenwater opvangen',
            'boom-of-haag' => 'Tuin vergroenen',
        ];

        $removed = [
            'energieneutrale-woning',
            'douche-warmteterugwinning',
            'kleine-windturbines',
            'overige-energie-maatregelen',
            'overige-groene-maatregelen',
        ];

        $measures = collect(
            RegulationService::init()->getFilters()['Measures']
        )->keyBy('Label');

        foreach ($movedOrChanged as $short => $new) {
            // Find the existing measure.
            $existingMeasure = MeasureCategory::where('short', $short)->first();
            if ($existingMeasure instanceof MeasureCategory) {
                $newShort = Str::slug($new);
                $newExistingMeasure = MeasureCategory::where('short', $newShort)->first();
                if (! $newExistingMeasure instanceof MeasureCategory) {
                    // It doesn't exist yet, so we can simply rename this one.
                    $existingMeasure->update([
                        'name' => [
                            'nl' => $new,
                        ],
                        'short' => $newShort,
                    ]);

                    // Now we must update the mapping related to have the proper data.
                    $syncData = [];
                    if ($measures->has($new)) {
                        $syncData[] = $measures[$new];
                    }

                    $mappingService
                        ->from($existingMeasure)
                        ->type(MappingHelper::TYPE_MEASURE_CATEGORY_VBJEHUIS)
                        ->sync($syncData, MappingHelper::TYPE_MEASURE_CATEGORY_VBJEHUIS);
                } else {
                    // Shame, it already exists. We will be removing the existing measure, but must change some mappings.
                    Mapping::where('target_model_type', MeasureCategory::class)
                        ->where('target_model_id', $existingMeasure->id)
                        ->orderBy('id')
                        ->eachById(function (Mapping $mapping) use ($newExistingMeasure) {
                            // So, we want to know if the new mapping exists for the current mapping's from. If not,
                            // we will update this one. If so, we will delete this one.

                            $newMappingExists = Mapping::where('type', $mapping->type)
                                ->where('from_model_type', $mapping->from_model_type)
                                ->where('from_model_id', $mapping->from_model_id)
                                ->where('target_model_type', MeasureCategory::class)
                                ->where('target_model_id', $newExistingMeasure->id)
                                ->exists();

                            if ($newMappingExists) {
                                $this->info("Deleting {$mapping->id} for {$mapping->toJson()}.");
                                $mapping->delete();
                            } else {
                                $this->info("Updating {$mapping->id}.");
                                $mapping->update([
                                    'target_model_type' => MeasureCategory::class,
                                    'target_model_id' => $newExistingMeasure->id,
                                ]);
                            }
                        });

                    $this->info("Deleting measure {$existingMeasure->toJson()}.");
                    $existingMeasure->delete();
                }
            }
        }

        foreach ($removed as $short) {
            // Find the existing measure so we can remove related mappings.
            $existingMeasure = MeasureCategory::where('short', $short)->first();
            if ($existingMeasure instanceof MeasureCategory) {
                $this->info("Deleting all mappings related to {$existingMeasure->toJson()}.");
                $total = Mapping::where(function($query) use ($existingMeasure) {
                    $query->where('target_model_type', MeasureCategory::class)
                        ->where('target_model_id', $existingMeasure->id);
                })->orWhere(function($query) use ($existingMeasure) {
                    $query->where('from_model_type', MeasureCategory::class)
                        ->where('from_model_id', $existingMeasure->id);
                })->delete();

                $this->info("Deleted {$total} mappings. Deleting measure {$existingMeasure->toJson()}.");
                $existingMeasure->delete();
            }
        }

        return self::SUCCESS;
    }
}