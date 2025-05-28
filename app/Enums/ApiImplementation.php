<?php

namespace App\Enums;

use App\Traits\Enums\EnumConcern;

enum ApiImplementation: string
{
    use EnumConcern;

    // Cased to match services namespace
    case LV_BAG = 'Lvbag';
    case EP_ONLINE = 'EpOnline';

    public function getSupport(): array
    {
        return match ($this) {
            self::LV_BAG,
            self::EP_ONLINE => [Country::NL],
        };
    }
}
