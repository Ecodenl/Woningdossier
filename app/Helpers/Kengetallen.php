<?php

namespace App\Helpers;

class Kengetallen
{
    // D95 (Energiekosten gas)
    // this is still being used, however it wont be called manually. See KengetallenService and related resolvers
    const EURO_SAVINGS_GAS = 1.45; // euro / m3 gas
    // D96 (Energiekosten elektra)
    const EURO_SAVINGS_ELECTRICITY = 0.40; // euro / kWh

    // D99 (CO2 Besparing gas)
    const CO2_SAVING_GAS = 1.88; // kg / m3 gas
    // D100 (CO2 Besparing elektra)
    const CO2_SAVINGS_ELECTRICITY = 0.335; // kg / kWh

    // D128 (Energiebesparing door aanbrengen kierdichting)
    const PERCENTAGE_GAS_SAVINGS_PLACE_CRACK_SEALING = 5; // %
    // D129 (Energiebesparing door vervangen kierdichting)
    const PERCENTAGE_GAS_SAVINGS_REPLACE_CRACK_SEALING = 1; // %

    // Energieinhoud gas
    // D116 (Kalorische waarde van het gas)
    const GAS_CALORIFIC_VALUE = 31.65; // MJ
    // D117 (Omrekenfactor MJ in kWh)
    const GAS_CONVERSION_FACTOR = 3.6; // MJ / kWh

    // Energiegebruik voor koken
    const ENERGY_USAGE_COOK_TYPE_GAS = 37; // m3
    const ENERGY_USAGE_COOK_TYPE_ELECTRIC = 225; // kWh
    const ENERGY_USAGE_COOK_TYPE_INDUCTION = 175; // kWh

    /**
     * gas (m3) / gasKwhPerM3() = kWh
     * kWh * gasKwhPerM3() = m3
     */
    public static function gasKwhPerM3(): float
    {
        return self::GAS_CALORIFIC_VALUE / self::GAS_CONVERSION_FACTOR;
    }
}
