<?php

namespace App\Services\SmartTwin\Mapping\Mappers;

use App\Models\Building;
use App\Services\SmartTwin\Mapping\FieldMapper;
use App\Services\SmartTwin\Mapping\Leaf;
use App\Services\SmartTwin\Mapping\MappingResult;

/**
 * SmartTwin's calculation for the dwelling now and after the package.
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

    public function paths(): array
    {
        return array_merge(
            array_map(fn (string $field) => "current.calculationResult.{$field}", self::FIELDS),
            array_map(fn (string $field) => "scenario.calculationResult.{$field}", self::FIELDS),
        );
    }

    public function map(Building $building, Leaf $leaf, array $response): MappingResult
    {
        return MappingResult::skipped(str_starts_with($leaf->path, 'current.')
            ? 'vastgelegd bij het pakket: de woning voor het pakket'
            : 'vastgelegd bij het pakket: de woning na het pakket');
    }
}
