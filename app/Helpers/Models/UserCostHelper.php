<?php

namespace App\Helpers\Models;

class UserCostHelper
{
    /**
     * Converts a user cost tool question short into a correctly dotted input name.
     *
     * @param string $short
     *
     * @return string
     */
    public static function resolveNameFromShort(string $short): string
    {
        // We need to construct the name. We know the structure, just not the short in the center.
        preg_match('/user-costs-(.*)-(subsidy|own)-total/', $short, $matches);
        return "user_costs.{$matches[1]}.{$matches[2]}_total";
    }
}