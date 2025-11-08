<?php

namespace App\Helpers;

class Kengetallen
{
    // D95 (Energiekosten gas)
    // NOTE: this is still being used, however it won't be called
    // manually. See KengetallenService and related resolvers (KengetallenCodes).
    const float EURO_SAVINGS_GAS = 1.42; // euro / m3 gas
    // D96 (Energiekosten elektra)
    const float EURO_SAVINGS_ELECTRICITY = 0.21; // euro / kWh

    // D99 (CO2 Besparing gas)
    const float CO2_SAVING_GAS = 1.88; // kg / m3 gas
    // D100 (CO2 Besparing elektra)
    const float CO2_SAVINGS_ELECTRICITY = 0.335; // kg / kWh

    // D128 (Energiebesparing door aanbrengen kierdichting)
    const int PERCENTAGE_GAS_SAVINGS_PLACE_CRACK_SEALING = 5; // %
    // D129 (Energiebesparing door vervangen kierdichting)
    const int PERCENTAGE_GAS_SAVINGS_REPLACE_CRACK_SEALING = 1; // %

    // Energieinhoud gas
    // D116 (Kalorische waarde van het gas)
    const float GAS_CALORIFIC_VALUE = 31.65; // MJ
    // D117 (Omrekenfactor MJ in kWh)
    const float GAS_CONVERSION_FACTOR = 3.6; // MJ / kWh

    // Energiegebruik voor koken
    const int ENERGY_USAGE_COOK_TYPE_GAS = 37; // m3
    const int ENERGY_USAGE_COOK_TYPE_ELECTRIC = 225; // kWh
    const int ENERGY_USAGE_COOK_TYPE_INDUCTION = 175; // kWh

    /**
     * gas (m3) / gasKwhPerM3() = kWh
     * kWh * gasKwhPerM3() = m3
     */
    public static function gasKwhPerM3(): float
    {
        return self::GAS_CALORIFIC_VALUE / self::GAS_CONVERSION_FACTOR;
    }
}
