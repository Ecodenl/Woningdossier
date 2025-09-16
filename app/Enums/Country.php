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
        return in_array($this, $api->getSupport());
    }

    public function getTranslation(): string
    {
        $code = strtolower($this->value);
        return __("default.countries.{$code}");
    }
}
