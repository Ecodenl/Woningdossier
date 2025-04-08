<?php

namespace App\Enums;

// TODO: Convert to ENUM
class Country
{
    const COUNTRY_NL = 'NL';
    const COUNTRY_BE = 'BE';

    // TODO: Make public when ENUM, protected to use __call!
    protected static function supportsApi(string $country, string $api): bool
    {
        if ($api === ApiImplementation::LV_BAG || $api === ApiImplementation::EP_ONLINE) {
            return self::COUNTRY_NL === $country;
        }

        return false;
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

    // TODO: Remove when ENUM
    protected string $country;

    public function __construct(string $country)
    {
        $this->country = $country;
    }

    public function __call(string $method, array $arguments)
    {
        return static::{$method}($this->country, ...$arguments);
    }
}