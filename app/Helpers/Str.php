<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;

class Str {

    const CHARACTERS = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";

	/**
	 * Uuid generation wrapping. Laravel < 5.6 uses Ramsey\Uuid. From 5.6 it is
	 * put in the \Illuminate\Support\Str helper.
	 * @return string
	 */
	public static function uuid(){
		$laravel = app();
		$laravel::VERSION;
		if (version_compare($laravel::VERSION, '5.6.0') < 0){
			// use
			return (string) Uuid::uuid4();
		}
		else {
			return (string) \Illuminate\Support\Str::uuid();
		}
	}

    /**
     * Generate a random password.
     *
     * @return string
     */
    public static function randomPassword()
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