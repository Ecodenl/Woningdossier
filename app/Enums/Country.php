<?php

namespace App\Enums;

use App\Traits\Enums\EnumConcern;

enum Country: string
{
    use EnumConcern;

    case NL = 'NL';
    case BE = 'BE';

    public function supportsApi(ApiImplementation $api): bool
    {
        if ($api === ApiImplementation::LV_BAG || $api === ApiImplementation::EP_ONLINE) {
            return $this === self::NL;
        }

        return false;
    }

    public function getTranslation(): string
    {
        $code = strtolower($this->value);
        return __("default.countries.{$code}");
    }
}