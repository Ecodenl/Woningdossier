<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Models\Building;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\MappingResult;

/**
 * SmartTwin's calculation for the dwelling now and after the package, and the subsidy for it.
 *
 * No question in Hoomdossier takes these: they describe the package as a whole, and are recorded
 * with it (see MappingApplier::recordPackage and SmartTwinPackage). The woonplan shows the gas saving
 * and the label after the package from there. Claimed so the report says where they went.
 */
class PackageResultsMapper implements FieldMapper
{
    /** ModelCalculationResult in the Advice API spec. */
    private const FIELDS = ['energyConsumptionGas', 'energyConsumptionElectricity', 'energyConsumptionWarmth',
                            'energyConsumptionWater', 'totalCo2Emission', 'totalElectricityProduction', 'energyLabel',
                            'heatDemand', 'heatingStandard', 'energyCostPerMonth'];

    /**
     * The subsidy, per scheme and in total. An empty list is a leaf of its own.
     */
    private const SUBSIDY_PATHS = ['scenario.totalSubsidy', 'scenario.subsidies', 'scenario.subsidies.*.name',
                                   'scenario.subsidies.*.amount'];

    public function paths(): array
    {
        return array_merge(
            array_map(fn (string $field) => "current.calculationResult.{$field}", self::FIELDS),
            array_map(fn (string $field) => "scenario.calculationResult.{$field}", self::FIELDS),
            self::SUBSIDY_PATHS,
        );
    }

    public function map(Building $building, Leaf $leaf, array $response): MappingResult
    {
        if (in_array($leaf->pathGroup, self::SUBSIDY_PATHS, true)) {
            return MappingResult::skipped('vastgelegd bij het pakket: de subsidie geldt het hele pakket, niet een losse maatregel');
        }

        return MappingResult::skipped(str_starts_with($leaf->path, 'current.')
            ? 'vastgelegd bij het pakket: de woning voor het pakket'
            : 'vastgelegd bij het pakket: de woning na het pakket');
    }
}
