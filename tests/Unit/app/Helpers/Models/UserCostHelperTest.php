<?php

namespace Tests\Unit\app\Helpers\Models;

use App\Helpers\Models\UserCostHelper;
use Tests\TestCase;
use ErrorException;

class UserCostHelperTest extends TestCase
{
    public static function resolveNameFromShortProvider()
    {
        return [
            ['user-costs-ventilation-decentral-wtw-subsidy-total', false, 'user_costs.ventilation-decentral-wtw.subsidy_total'],
            ['user-costs-full-heat-pump-ground-heat-subsidy-total', false, 'user_costs.full-heat-pump-ground-heat.subsidy_total'],
            ['user-costs-roof-insulation-pitched-inside-own-total', false, 'user_costs.roof-insulation-pitched-inside.own_total'],
            ['user-costs-roof-insulation-pitched-replace-tiles-subsidy-total', false, 'user_costs.roof-insulation-pitched-replace-tiles.subsidy_total'],
            ['user-costs-heat-pump-boiler-place-replace-own-total', false, 'user_costs.heat-pump-boiler-place-replace.own_total'],
            ['not-user-cost', true, null],
            ['random string', true, null],
        ];
    }

    /**
     * @dataProvider resolveNameFromShortProvider
     */
    public function test_resolve_name_from_short($short, $fail, $expected)
    {
        if ($fail) {
            $this->expectException(ErrorException::class);
        }

        $result = UserCostHelper::resolveNameFromShort($short);

        // If we expect an exception, the result will never be evaluated.
        $this->assertEquals($expected, $result);
    }

    public static function resolveMeasureAndTypeFromShortProvider()
    {
        return [
            ['user-costs-ventilation-decentral-wtw-subsidy-total', false, ['ventilation-decentral-wtw', 'subsidy']],
            ['user-costs-full-heat-pump-ground-heat-subsidy-total', false, ['full-heat-pump-ground-heat', 'subsidy']],
            ['user-costs-roof-insulation-pitched-inside-own-total', false, ['roof-insulation-pitched-inside', 'own']],
            ['user-costs-roof-insulation-pitched-replace-tiles-subsidy-total', false, ['roof-insulation-pitched-replace-tiles', 'subsidy']],
            ['user-costs-heat-pump-boiler-place-replace-own-total', false, ['heat-pump-boiler-place-replace', 'own']],
            ['not-user-cost', true, null],
            ['random string', true, null],
        ];
    }

    /**
     * @dataProvider resolveNameFromShortProvider
     */
    public function test_resolve_measure_and_type_from_short($short, $fail, $expected)
    {
        if ($fail) {
            $this->expectException(ErrorException::class);
        }

        $result = UserCostHelper::resolveNameFromShort($short);

        // If we expect an exception, the result will never be evaluated.
        $this->assertEquals($expected, $result);
    }
}
