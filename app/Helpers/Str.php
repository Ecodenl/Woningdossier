<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;
use Illuminate\Support\Str as SupportStr;

class Str extends \Illuminate\Support\Str
{
    const CHARACTERS = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789';
    const CONSIDERED_EMPTY_ANSWERS = ['', null, 'null', '0.00', '0.0', '0', 0, '0,0', '0,00'];

    /**
     * Uuid generation wrapping. Laravel < 5.6 uses Ramsey\Uuid. From 5.6 it is
     * put in the \Illuminate\Support\Str helper.
     */
    public static function uuid(): string
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
     * Check if a given string is a valid UUID.
     *
     * @param string $uuid The string to check
     */
    public static function isValidUuid(string $uuid): bool
    {
        if (! is_string($uuid) || (1 !== preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/', $uuid))) {
            return false;
        }

        return true;
    }

    /**
     * Check if a given string is a valid GUID.
     *
     * https://stackoverflow.com/questions/1253373/php-check-for-valid-guid/#answer-1515456
     *
     * @param $guid
     */
    public static function isValidGuid($guid): bool
    {
        if (preg_match('/^(\{)?[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}(?(1)\})$/i', $guid)) {
            return true;
        }

        return false;
    }

    /**
     * Check if a string contains a pipe.
     *
     * @param $string
     */
    public static function isPiped($string): bool
    {
        if (count(explode('|', $string)) > 1) {
            return true;
        }

        return false;
    }

    /**
     * Returns a random UUID. Only used as a fallback in case all other methods
     * don't work.
     */
    protected static function randomUuid(): string
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
     */
    public static function randomPassword(): string
    {
        $password = [];
        $characterLength = strlen(self::CHARACTERS) - 1;

        for ($i = 0; $i < 12; ++$i) {
            $n = rand(0, $characterLength);
            $password[] = self::CHARACTERS[$n];
        }

        return (string) implode($password); // password returns array so implode it
    }

    /**
     * Check if the given answer is an empty answer, its considered empty when it's in the array.
     *
     * @param $answer
     */
    public static function isConsideredEmptyAnswer($answer): bool
    {
        if (in_array($answer, self::CONSIDERED_EMPTY_ANSWERS, true)) {
            return true;
        }

        return false;
    }

    public static function lcfirst($string)
    {
        return SupportStr::lower(SupportStr::substr($string, 0, 1)).SupportStr::substr($string, 1);
    }

    public static function isValidJson($value, $arrayOnly = true): bool
    {
        $json = json_decode($value, true);

        // There could be JSON strings or numeric values, we don't want them to be valid if $arrayOnly is true.
        return ! is_null($json) && ($arrayOnly === false || ($arrayOnly === true && is_array($json)));
    }

    /**
     * Check if a needle is somewhere (partially) in an array.
     *
     * @param  $needle
     */
    public static function arrContains(array $array, $needle, bool $ignoreCase = false): bool
    {
        $needle = $ignoreCase ? strtolower($needle) : $needle;

        if (! empty($array)) {
            // Dot to remove recursion
            $array = Arr::dot($array);

            foreach ($array as $key => $value) {
                if (is_string($value)) {
                    $value = $ignoreCase ? strtolower($value) : $value;
                    if (SupportStr::contains($value, $needle)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check if a needle is somewhere at the start in an array.
     *
     * @param $needle
     */
    public static function arrStartsWith(array $array, $needle, bool $ignoreCase = false): bool
    {
        $needle = $ignoreCase ? strtolower($needle) : $needle;

        if (! empty($array)) {
            // Dot to remove recursion
            $array = Arr::dot($array);

            foreach ($array as $key => $value) {
                if (is_string($value)) {
                    $value = $ignoreCase ? strtolower($value) : $value;
                    if (SupportStr::startsWith($value, $needle)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Check if a needle is somewhere at the start of the keys in an array.
     *
     * @param $needle
     */
    public static function arrKeyStartsWith(array $array, $needle, bool $ignoreCase = false): bool
    {
        return static::arrStartsWith(array_keys($array), $needle, $ignoreCase);
    }

    /**
     * Convert HTML array format to dot
     */
    public static function htmlArrToDot(string $htmlArray): string
    {
        $dotted = str_replace(']', '', str_replace('[', '.', $htmlArray));
        if (substr($htmlArray, -2) === '[]') {
            $dotted = substr($dotted, 0, strlen($dotted) - 1);
        }
        return $dotted;
    }

    /**
     * Convert a dotted string to a valid HTML input name.
     */
    public static function dotToHtml(?string $dottedName, bool $asArray = false): ?string
    {
        // No use for an empty string
        if (empty($dottedName)) {
            return $dottedName;
        }

        $htmlName = \Illuminate\Support\Str::of($dottedName);

        // Nothing to replace if no dots
        if ($htmlName->contains('.')) {
            $htmlName = $htmlName->replaceFirst('.', '[') // Replace first dot with opening bracket
            ->replace('*', '') // Remove wildcards
            ->replace('.', '][') // Replace other dots with separating brackets
            ->append(']'); // Add final bracket
        }

        if ($asArray) {
            // Append HTML array if requested
            $htmlName = $htmlName->append('[]');
        }

        return $htmlName;
    }

    /**
     * Check if a string has replaceables.
     */
    public static function hasReplaceables(string $string): bool
    {
        // See https://regex101.com/r/ckEDG4/1
        $pattern = ':{1}([\w]*)';

        preg_match("/{$pattern}/i", $string, $matches);

        return ! empty($matches);
    }

    /**
     * Prepare a JSON string for dropping in HTML.
     * TODO: Tests
     */
    public static function prepareJsonForHtml(string $json): string
    {
        // TODO: Just use {{ json_encode() }} in blade!
        return str_replace('"', '\'', $json);
    }
}
