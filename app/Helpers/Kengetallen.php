<?php

namespace App\Helpers;

class Kengetallen
{
    // D95
    const EURO_SAVINGS_GAS = 0.79; // euro / m3 gas
    // D96
    const EURO_SAVINGS_ELECTRICITY = 0.23; // euro / kWh
    // D99
    const CO2_SAVING_GAS = 1.88; // kg / m3 gas
    // D100
    const CO2_SAVINGS_ELECTRICITY = 0.335; // kg / kWh

    // D128
    const PERCENTAGE_GAS_SAVINGS_PLACE_CRACK_SEALING = 5; // %
    // D129
    const PERCENTAGE_GAS_SAVINGS_REPLACE_CRACK_SEALING = 1; // %

    // Energieinhoud gas
    // D116
    const GAS_CALORIFIC_VALUE = 31.65; // MJ
    // D117
    const GAS_CONVERSION_FACTOR = 3.6; // MJ / kWh

    public static function gasKwhPerM3()
    {
        return self::GAS_CALORIFIC_VALUE / self::GAS_CONVERSION_FACTOR;
    }
}
