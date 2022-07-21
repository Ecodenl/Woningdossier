<?php

namespace App\Helpers\Models;

use App\Models\Cooperation;
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

    public static function getSettingValue(Cooperation $cooperation, string $short, string $default = null)
    {
        return optional(
                $cooperation->cooperationSettings()
                    ->forShort($short)
                    ->first()
            )->value ?? $default;
    }

    public static function syncSettings(Cooperation $cooperation, array $data)
    {
        foreach ($data as $short => $value) {
            CooperationSetting::updateOrCreate(
                ['short' => $short, 'cooperation_id' => $cooperation->id],
                compact('value'),
            );
        }
    }
}