<?php

namespace App\Helpers;

class ObjectHelper
{
    /**
     * Get the property of an object if it exists, save the countless inline repeated boilerplate code.
     *
     * @param $object
     * @param $property
     *
     * @return mixed
     */
    public static function getObjectProperty($object, $property)
    {
        if (! is_null($object) && $object instanceof \stdClass) {
            if (property_exists($object, $property)) {
                return $object->{$property};
            }
        }

        return null;
    }
}