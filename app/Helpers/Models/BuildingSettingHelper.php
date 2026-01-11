<?php

namespace App\Helpers\Models;

use App\Models\Building;
use App\Models\BuildingSetting;

class BuildingSettingHelper
{
    const SHORT_SMALL_MEASURES_ENABLED_QUICK_SCAN = 'small_measures_enabled_quick-scan';
    const SHORT_SMALL_MEASURES_ENABLED_LITE_SCAN = 'small_measures_enabled_lite-scan';

    public static function getAvailableSettings(): array
    {
        return [
            static::SHORT_SMALL_MEASURES_ENABLED_QUICK_SCAN => 'boolean',
            static::SHORT_SMALL_MEASURES_ENABLED_LITE_SCAN => 'boolean',
        ];
    }

    public static function getSettingValue(Building $building, string $short, mixed $default = null): mixed
    {
        return optional(
            $building->buildingSettings()
                ->forShort($short)
                ->first()
        )->value ?? $default;
    }

    public static function syncSettings(Building $building, array $data): void
    {
        foreach ($data as $short => $value) {
            if (is_null($value)) {
                BuildingSetting::where([
                    'building_id' => $building->id,
                    'short' => $short,
                ])->delete();
            } else {
                BuildingSetting::updateOrCreate(
                    ['short' => $short, 'building_id' => $building->id],
                    ['value' => $value],
                );
            }
        }
    }

    public static function hasOverride(Building $building, string $short): bool
    {
        return $building->buildingSettings()
            ->forShort($short)
            ->exists();
    }
}
