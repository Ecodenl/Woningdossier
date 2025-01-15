<?php

namespace App\Helpers;

use App\Models\Building;
use App\Models\Cooperation;
use App\Models\InputSource;
use App\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class HoomdossierSession extends Session
{
    /**
     * Set all the required values.
     */
    public static function setHoomdossierSessions(Building $building, InputSource $inputSource, InputSource $inputSourceValue, Role $role): void
    {
        self::setBuilding($building);
        self::setInputSource($inputSource);
        self::setInputSourceValue($inputSourceValue);
        self::setRole($role);
        self::setCompareInputSourceShort($inputSource->short);
        // default to false
        self::setIsUserComparingInputSources(false);

        self::setIsObserving(false);
    }

    /**
     * Destroy the hoomdossier sessions.
     */
    public static function destroy()
    {
        self::forget(['hoomdossier_session']);
    }

    /**
     * Set the Cooperation id.
     */
    public static function setCooperation(Cooperation $cooperation)
    {
        self::put('cooperation', $cooperation->id);
    }

    /**
     * Check if the cooperation session is set.
     */
    public static function hasCooperation(): bool
    {
        if (self::has('cooperation')) {
            return true;
        }

        return false;
    }

    /**
     * @param bool $object Set to true if you want to get the hydrated cooperation model
     */
    public static function getCooperation(bool $object = false): int|Cooperation|null
    {
        $cooperation = self::get('cooperation');

        // if there is no cooperation set and the application is not running in the console, we have a serious issue.
        if (! is_int($cooperation) && ! app()->runningInConsole()) {
            Log::error('Cooperation was not an integer!!');
            Log::error($cooperation);
        }

        if ($object) {
            $cooperation = \App\Helpers\Cache\Cooperation::find($cooperation);
        }

        return $cooperation;
    }

    /**
     * Function to set Hoomdossier sessions.
     */
    public static function setHoomdossierSession(string $key, mixed $value): void
    {
        self::put('hoomdossier_session.' . $key, $value);
    }

    /**
     * Get a value from the Hoomdossier session.
     */
    public static function getHoomdossierSession(string $key, mixed $default = null): mixed
    {
        return self::get('hoomdossier_session.' . $key, $default);
    }

    /**
     * Returns whether or not this session contains a current role.
     */
    public static function hasRole(): bool
    {
        return ! empty(self::getRole());
    }

    /**
     * Set the role.
     */
    public static function setRole(Role $role): void
    {
        self::setHoomdossierSession('role_id', $role->id);
    }

    /**
     * Set the observing boolean.
     *
     * @NOTE key meant to determine if a user is observing someones tool / building.
     */
    public static function setIsObserving(bool $observing = false): void
    {
        self::setHoomdossierSession('is_observing', $observing);
    }

    /**
     * Check if a user is observing someones tool / building.
     */
    public static function getIsObserving(): bool
    {
        return self::getHoomdossierSession('is_observing', false);
    }

    /**
     * Set the input source value id.
     *
     * @NOTE: this is not the same as the input source, this input source will be used to get the right values for the form.
     */
    public static function setInputSourceValue(InputSource $inputSource): void
    {
        self::setHoomdossierSession('input_source_value_id', $inputSource->id);
    }

    /**
     * Set the input source id.
     */
    public static function setInputSource(InputSource $inputSource): void
    {
        self::setHoomdossierSession('input_source_id', $inputSource->id);
    }

    /**
     * Set the bool, this determines if the logged in user is comparing input sources.
     */
    public static function setIsUserComparingInputSources(bool $isUserComparing): void
    {
        self::setHoomdossierSession('is_user_comparing_input_sources', $isUserComparing);
    }

    /**
     * Set the compare input source short, this is used to retrieve the right compare value from the dom.
     */
    public static function setCompareInputSourceShort(string $inputSourceShort): void
    {
        self::setHoomdossierSession('compare_input_source_short', $inputSourceShort);
    }

    /**
     * Stop / Reset the sessions for comparing the input sources
     * We set the compareInputSourceShort back to the auth user his own
     * input source short and the isUserComparingInputSource back to false.
     */
    public static function stopUserComparingInputSources(): void
    {
        self::setIsUserComparingInputSources(false);
        self::setCompareInputSourceShort(InputSource::find(self::getInputSource())->short);
    }

    /**
     * Get the compare input source.
     */
    public static function getCompareInputSourceShort(): string
    {
        return self::getHoomdossierSession('compare_input_source_short', '');
    }

    /**
     * Set the building id.
     */
    public static function setBuilding(Building $building): void
    {
        self::setHoomdossierSession('building_id', $building->id);
    }

    /**
     * Returns the role or role_id.
     *
     * @param bool $object Set to true if you want to the role model returned
     */
    public static function getRole(bool $object = false): int|Role|null
    {
        $id = self::getHoomdossierSession('role_id');
        if (! $object) {
            return $id;
        }

        return \App\Helpers\Cache\Role::find($id);
    }

    public static function currentRoleIs($role): bool
    {
        if (! (\App\Helpers\Cache\Role::findByName($role) instanceof Role)) {
            return false;
        }

        $currentRole = self::getRole(true)?->name;

        return $currentRole == $role;
    }

    /**
     * Get the input source id.
     *
     * @param bool $object Set to true if you want an object returned
     *
     * @return int|InputSource|null
     */
    public static function getInputSource(bool $object = false): int|InputSource|null
    {
        $id = self::getHoomdossierSession('input_source_id');
        if (! $object) {
            return $id;
        }

        return \App\Helpers\Cache\InputSource::find($id);
    }

    /**
     * Get the input source value id
     * Read the NOTE @setInputSourceValue.
     *
     * @return int|InputSource|null
     */
    public static function getInputSourceValue($object = false)
    {
        $id = self::getHoomdossierSession('input_source_value_id');

        if (! $object) {
            return $id;
        }

        return \App\Helpers\Cache\InputSource::find($id);
    }

    /**
     * Get the current building ID (or building model if hydrated).
     */
    public static function getBuilding(bool $hydrate = false): null|int|Building
    {
        $building = self::getHoomdossierSession('building_id');
        if ($hydrate && ! empty($building)) {
            $building = \App\Helpers\Cache\Building::find($building);
        }

        return $building;
    }

    /**
     * Returns if a user is comparing input sources.
     */
    public static function getIsUserComparingInputSources(): bool
    {
        return self::getHoomdossierSession('is_user_comparing_input_sources', false);
    }

    /**
     * Check if a user is comparing his own values against the values from a other input source.
     */
    public static function isUserComparingInputSources(): bool
    {
        return self::getIsUserComparingInputSources();
    }

    /**
     * Check if a user is NOT comparing his own values against the values from a other input source.
     */
    public static function isUserNotComparingInputSources(): bool
    {
        return ! self::getIsUserComparingInputSources();
    }

    /**
     * Check if a user is observing a building / tool.
     */
    public static function isUserObserving(): bool
    {
        return self::getIsObserving();
    }

    /**
     * Return the Hoomdossier session data.
     */
    public static function getAll(): array
    {
        return (array) self::get('hoomdossier_session');
    }

    public static function switchRole(Building $building, Role $role): void
    {
        Log::debug('Switching roles from ' . static::getRole() . ' to ' . $role->id);

        // set the new sessions!
        static::setRole($role);
        if ($role->inputSource instanceof InputSource) {
            static::setInputSource($role->inputSource);
            static::setInputSourceValue($role->inputSource);
        }

        static::setBuilding($building);
        static::setIsObserving(false);
        static::setIsUserComparingInputSources(false);
    }
}
