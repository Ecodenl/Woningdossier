<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;

class Str {
    const CHARACTERS = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";

	/**
	 * Uuid generation wrapping. Laravel < 5.6 uses Ramsey\Uuid. From 5.6 it is
	 * put in the \Illuminate\Support\Str helper.
     *
	 * @return string
	 */
    public static function uuid() : string
    {
        $laravel = app();
        if (version_compare($laravel::VERSION, '5.6.0') < 0) {
            try {
                return (string) Uuid::uuid4();
            } catch (\Exception $e) {
                return (string) self::randomUuid();
            }
        } else {
            return (string) \Illuminate\Support\Str::uuid();
        }
    }

    /**
     * Returns a random UUID. Only used as a fallback in case all other methods
     * don't work.
     *
     * @return string
     */
    protected static function randomUuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Generate a random password.
     *
     * @return string
     */
    public static function randomPassword() : string
    {
        $password = [];
        $characterLength = strlen(self::CHARACTERS) -1;

        for ($i = 0; $i < 12; $i++) {
            $n = rand(0, $characterLength);
            $password[] = self::CHARACTERS[$n];
        }

        return (string) implode($password); // password returns array so implode it
    }
}

