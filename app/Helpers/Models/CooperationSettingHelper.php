<?php

namespace App\Helpers\Models;

use App\Models\CooperationSetting;
use Illuminate\Database\Eloquent\Collection;

class CooperationSettingHelper
{
    const SHORT_REGISTER_URL = 'register_url';

    public static function getAvailableSettings(): array
    {
        return [
            static::SHORT_REGISTER_URL => static::SHORT_REGISTER_URL,
        ];
    }

    public static function getCooperationSettings(int $cooperationId): Collection
    {
        return CooperationSetting::where('cooperation_id', $cooperationId)->get();
    }

    public static function getSettingValue(int $cooperationId, string $short)
    {
        return optional(CooperationSetting::where('coopereration_id', $cooperationId)
            ->forShort($short)
            ->first())->value;
    }

    public static function syncSettings(int $cooperationId, array $data)
    {
        foreach ($data as $short => $value) {
            CooperationSetting::updateOrCreate(
                ['short' => $short, 'cooperation_id' => $cooperationId],
                compact('value'),
            );
        }
    }
}