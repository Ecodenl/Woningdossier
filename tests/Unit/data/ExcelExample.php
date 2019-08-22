<?php

namespace Tests\Unit\data;

use App\Models\BuildingFeature;
use App\Models\BuildingHeating;
use App\Models\BuildingService;
use App\Models\BuildingType;
use App\Models\ComfortLevelTapWater;
use App\Models\Service;
use App\Models\ServiceValue;
use App\Models\User;
use App\Models\UserEnergyHabit;

class ExcelExample
{
    // These fixtures come from the original Excel example that was the basis
    // of this tool and it's calculations. We use this to check if the outcomes
    // of the tool match the outcomes of the Excel file.

    public static function building()
    {
        // 'first_name', 'last_name', 'email', 'password', 'phone_number',
        //        'confirm_token', 'old_email', 'old_email_token'

        $u = self::user();

        // 'street', 'number', 'city', 'postal_code', 'bag_addressid', 'building_coach_status_id', 'extension', 'is_active'
        $b = new \App\Models\Building([
            'street'      => 'Straat', 'number' => 1, 'city' => 'Nowhere',
            'postal_code' => '2013 BC', 'bag_addressid' => '01234',
        ]);
        $b->primary = 1;

        $b->user()->associate($u);
        $b->save();

        // 'element_values', 'plastered_wall_surface','building_id','wall_joints',
        // 'cavity_wall', 'contaminated_wall_joints','wall_surface',
        // 'insulation_wall_surface','damage_paintwork', 'additional_info',
        // 'building_layers','surface','floor_surface','monument',
        // 'insulation_surface','build_year','input_source_id',
        // 'facade_plastered_painted','facade_plastered_surface_id',
        // 'facade_damaged_paintwork_id','window_surface','roof_type_id',
        $bf = new BuildingFeature([
        ]);

        $buildingType = new BuildingType();
        $buildingType->name = 'Tussenwoning';
        $buildingType->calculate_value = 2;
        $buildingType->save();

        $bf->buildingType()->associate($buildingType);
        $bf->building()->associate($b);

        // service
        $serviceHrBoiler = Service::find(4);
        $serviceValueHrBoiler = ServiceValue::find(10); // aanwezig, recent vervangen

        $hrBoiler = new BuildingService();
        $hrBoiler->service()->associate($serviceHrBoiler);
        $hrBoiler->serviceValue()->associate($serviceValueHrBoiler);
        $hrBoiler->building()->associate($b);

        return $b;
    }

    /**
     * @return User
     */
    public static function user()
    {
        $u = User::firstOrCreate(['first_name' => 'Foo', 'last_name' => 'Bar'], ['phone_number' => '1111111111', ]);
        /* @var User $u */
        return $u;
    }

    /**
     * @return UserEnergyHabit
     */
    public static function userEnergyHabits()
    {
        // 'user_id','input_source_id','resident_count',
        // 'thermostat_high','thermostat_low','hours_high',
        // 'heating_first_floor','heating_second_floor','cook_gas',
        // 'water_comfort_id','amount_electricity','amount_gas',
        // 'amount_water','living_situation_extra','motivation_extra',
        $user = self::user();

        $habits = UserEnergyHabit::firstOrCreate(['user_id' => $user->id], [
            'resident_count' => 2,
            'thermostat_high' => 20,
            'thermostat_low' => 16,
            'hours_high' => 12,
            // heating_first_floor -> relation
            // heating_second_floor -> relation
            'cook_gas' => 1, // ja
            // water_comfort_id -> relation
            'amount_electricity' => 3018,
            'amount_gas' => 1398,
        ]);
        // Verwarmd, de meeste radiatoren staan aan
        $hff = new BuildingHeating();
        $hff->degree = 18;
        $hff->calculate_value = 2;
        $hff->is_default = 0;
        $habits->heatingFirstFloor()->associate($hff);

        // Matig verwarmd, de meeste radiatoren staan hoger dan * of een aantal radiatoren staan hoog
        $hsf = new BuildingHeating();
        $hsf->degree = 13;
        $hsf->calculate_value = 3;
        $hsf->is_default = 0;
        $habits->heatingSecondFloor()->associate($hsf);

        $wc = new ComfortLevelTapWater();
        $wc->calculate_value = 2; // Comfort
        $habits->comfortLevelTapWater()->associate($wc);

        $habits->user()->associate($user);

        $habits->save();

        return $habits;
    }
}
