<?php

namespace App\Enums;

// TODO: Convert to ENUM
class Country
{
    const COUNTRY_NL = 'NL';
    const COUNTRY_BE = 'BE';

    public static function supportsApi(string $country): bool
    {
        return self::COUNTRY_NL === $country;
    }

    public static function getTranslation(string $county): string
    {
        $code = strtolower($county);
        return __("default.countries.{$code}");
    }

    public static function cases(): array
    {
        return [
            self::COUNTRY_NL,
            self::COUNTRY_BE,
        ];
    }
}