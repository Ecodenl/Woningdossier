<?php

namespace Tests\Unit\app\Helpers\Models;

use App\Helpers\Models\UserCostHelper;
use Tests\TestCase;

class UserCostHelperTest extends TestCase
{
    protected bool $usesDatabase = false;

    public static function resolveNameFromShortProvider()
    {
        return [
            ['user-costs-ventilation-decentral-wtw-subsidy-total', 'user_costs.ventilation-decentral-wtw.subsidy_total'],
            ['user-costs-full-heat-pump-ground-heat-subsidy-total', 'user_costs.full-heat-pump-ground-heat.subsidy_total'],
            ['user-costs-roof-insulation-pitched-inside-own-total', 'user_costs.roof-insulation-pitched-inside.own_total'],
            ['user-costs-roof-insulation-pitched-replace-tiles-subsidy-total', 'user_costs.roof-insulation-pitched-replace-tiles.subsidy_total'],
            ['user-costs-heat-pump-boiler-place-replace-own-total', 'user_costs.heat-pump-boiler-place-replace.own_total'],
        ];
    }

    /**
     * @dataProvider resolveNameFromShortProvider
     */
    public function test_resolve_name_from_short($short, $expected)
    {
        $this->assertEquals($expected, UserCostHelper::resolveNameFromShort($short));
    }
}
