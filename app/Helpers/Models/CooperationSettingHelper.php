<?php

namespace App\Helpers\Models;

use App\Models\CooperationSetting;

class CooperationSettingHelper
{
    const SHORT_REGISTER_URL = 'register-url';

    public static function getValue(int $cooperationId, string $short)
    {
        return optional(CooperationSetting::where('coopereration_id', $cooperationId)
            ->forShort($short)
            ->first())->value;
    }
}