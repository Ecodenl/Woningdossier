<?php

namespace App\Rules;

class LocaleBasedRule
{
    /**
     * The locale of the country for which this rule will be applied.
     *
     * @var string
     */
    protected $country;

    /**
     * LocaleBasedRule constructor.
     *
     * @param string $countryIso3166alpha2 Basically a locale
     */
    public function __construct($countryIso3166alpha2 = null)
    {
        if (is_null($countryIso3166alpha2)) {
            $countryIso3166alpha2 = app()->getLocale();
        }
        $this->country = strtolower($countryIso3166alpha2);
    }
}
