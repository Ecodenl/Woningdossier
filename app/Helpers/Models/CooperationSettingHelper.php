<?php

namespace App\Helpers\Models;

use App\Models\Cooperation;
use App\Models\CooperationSetting;
use Illuminate\Database\Eloquent\Collection;

class CooperationSettingHelper
{
    const SHORT_REGISTER_URL = 'register_url';
    const SHORT_VERIFICATION_EMAIL_TEXT = 'verification_email_text';

    public static function getAvailableSettings(): array
    {
        return [
            static::SHORT_REGISTER_URL => 'input',
            static::SHORT_VERIFICATION_EMAIL_TEXT => 'textarea',
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
