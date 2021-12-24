<?php

namespace App\Helpers;

class Kengetallen
{
    // D95 (Energiekosten gas)
    const EURO_SAVINGS_GAS = 0.98; // euro / m3 gas
    // D96 (Energiekosten elektra)
    const EURO_SAVINGS_ELECTRICITY = 0.28; // euro / kWh
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

    public static function gasKwhPerM3()
    {
        return self::GAS_CALORIFIC_VALUE / self::GAS_CONVERSION_FACTOR;
    }
}
