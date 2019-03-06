<?php

namespace App\Helpers;

/**
 * Helper class that can be use full when the old data needs to be modified.
 *
 * Class Old
 */
class Old
{
    protected static function setOld($key, $value)
    {
        session()->flash('_modified_old_input.'.$key, $value);
    }

    protected static function getOld($key)
    {
        return session()->get('_modified_old_input.'.$key);
    }

    /**
     * Determine if a given key exists in the _modified_old_input session.
     *
     * @param $key
     *
     * @return bool
     */
    protected static function hasOld($key): bool
    {
        return (bool) session()->has('_modified_old_input.'.$key);
    }

    /**
     * Set a value with key in the _modified_old_input session.
     *
     * @param $key | key to be set in the _modified_old_input session
     * @param $value | value to be set in the given key
     */
    public static function put($value, $key = '')
    {
        if (empty($key)) {
            session()->flash('_modified_old_input', $value);
        } else {
            self::setOld($key, $value);
        }
    }

    /**
     * Get a value from the _modified_old_input session.
     *
     * @param string $key     | old value to retrieve
     * @param string $default | if the key does not exist in the session, return the default value
     *
     * @return mixed|string
     */
    public static function get($key = '', $default = '')
    {
        if (empty($key)) {
            return self::all();
        } elseif (self::hasOld($key)) {
            $modifiedOldInput = array_dot(self::all());

            return array_get($modifiedOldInput, $key);
        } else {
            return $default;
        }
    }

    /**
     * Retrieve all the _modified_old_input data.
     *
     * @return mixed
     */
    public static function all()
    {
        return session()->get('_modified_old_input');
    }
}
