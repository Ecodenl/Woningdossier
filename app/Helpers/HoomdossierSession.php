<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Role;

class HoomdossierSession extends Session {


    /**
     * Set the Cooperation id
     *
     * @param Cooperation $cooperation
     */
    public static function setCooperation(Cooperation $cooperation)
    {
        self::put('cooperation', $cooperation->id);
    }

    /**
     * Check if the cooperation session is set
     *
     * @return bool
     */
    public static function hasCooperation(): bool
    {
        if (self::has('cooperation')) {
            return true;
        }

        return false;
    }

    public static function getCooperation(): int
    {
        return self::get('cooperation');
    }

    /**
     * Function to set Hoomdossier sessions.
     *
     * @param $key
     * @param $value
     */
    public static function setHoomdossierSession($key, $value)
    {
        self::put('hoomdossier_session.'.$key, $value);
    }

    /**
     * Get a value from the Hoomdossier session
     *
     * @param $key
     * @return mixed
     */
    public static function getHoomdossierSession($key)
    {
        return self::get('hoomdossier_session.'.$key);
    }

    /**
     * Set the role
     *
     * @param Role $role
     */
    public static function setRole(Role $role)
    {
        self::setHoomdossierSession('role_id', $role->id);
    }

    /**
     * Set the input source id
     *
     * @param InputSource $inputSource
     */
    public static function setInputSource(InputSource $inputSource)
    {
        self::setHoomdossierSession('input_source_id', $inputSource->id);

    }

    /**
     * Set the building id
     *
     * @param Building $building
     */
    public static function setBuilding(Building $building): void
    {
        self::setHoomdossierSession('building_id', $building->id);
    }

    public static function getRole(): int
    {
        return self::getHoomdossierSession('role_id');
    }

    /**
     * Get the input source id
     *
     * @return int
     */
    public static function getInputSource(): int
    {
        return self::getHoomdossierSession('input_source_id');
    }

    /**
     * Get the building id
     *
     * @return int
     */
    public static function getBuilding(): int
    {
        return self::getHoomdossierSession('building_id');
    }
}