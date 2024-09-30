<?php

namespace App\Helpers;

class NumberFormatter
{
    /**
     * Separators for viewing (so, mostly used in the views or data for the view).
     *
     * @var array
     */
    protected static $formatLocaleSeparators = [
        'nl' => [
            'decimal' => ',',
            'thousands' => '.',
        ],
        'en' => [
            'decimal' => '.',
            'thousands' => ',',
        ],
    ];

    /**
     * For reversing from view to controller. We could define just the differences
     * from the $formatLocaleSeparators, but that might be more confusing than
     * just duplicating.
     *
     * @var array
     */
    protected static $reverseLocaleSeparators = [
        'nl' => [
            'decimal' => ',',
            'thousands' => '',
            // different! If people fill in a dot, treat it like a comma (and so: a decimal)
        ],
        'en' => [
            'decimal' => '.',
            'thousands' => ',',
        ],
    ];

    /**
     * Round a number.
     *
     * @param $number
     * @param int $bucket
     *
     * @return float|int
     */
    public static function round($number, int $bucket = 5)
    {
        $bucket = $bucket <= 0 ? 1 : $bucket;

        if (! is_numeric($number)) {
            $number = static::reverseFormat($number);
        }

        $result = round($number / $bucket) * $bucket;

        if (substr($result, 0, 1) === '-' && $result == 0) {
            return 0;
        }

        return $result;
    }

    /**
     * Used to format the given number in a human-readable format, mainly used for frontend display.
     *
     * @param $number
     * @param int   $decimals
     * @param false $shouldRoundNumber
     *
     * @return int|string
     */
    public static function format($number, int $decimals = 0, bool $shouldRoundNumber = false)
    {
        $locale = app()->getLocale();
        if (is_null($number)) {
            $number = 0;
        }

        // if the number is numeric we can format it
        // else we return the value that's not a correct number
        if (is_numeric($number)) {
            if ($shouldRoundNumber) {
                $number = static::round($number);
            }

            $formattedNumber = number_format(
                $number,
                $decimals,
                self::$formatLocaleSeparators[$locale]['decimal'],
                self::$formatLocaleSeparators[$locale]['thousands']
            );

            return $formattedNumber;
        } else {
            return $number;
        }
    }

    /**
     * @param $number
     * @param int $decimals
     *
     * @return string
     */
    public static function mathableFormat($number, int $decimals = 0): string
    {
        $number = str_replace(',', '.', $number);

        if (is_numeric($number)) {
            $number = number_format($number, $decimals, '.', '');
        }

        return $number;
    }

    public static function reverseFormat($number)
    {
        $locale = app()->getLocale();
        if (is_null($number)) {
            $number = 0;
        }

        $number = self::removeMultipleDecimals($number);

        $number = str_replace(
            [self::$reverseLocaleSeparators[$locale]['thousands'], ' '],
            ['', ''],
            $number
        );

        $result = str_replace(
            self::$reverseLocaleSeparators[$locale]['decimal'],
            '.',
            $number
        );

        // In some cases, the given number might not be an actual number. We want to force it as one.
        if (! is_numeric($result)) {
            $result = 0;
        }

        return $result;
    }

    public static function range($from, $to, $decimals = 0, $separator = ' - ', $prefix = '')
    {
        $from = static::mathableFormat($from, $decimals);
        $from = static::format($from, $decimals);

        $to = static::mathableFormat($to, $decimals);
        $to = static::format($to, $decimals);

        if (! empty($from) && empty($to) && ! is_numeric($to)) {
            return static::prefix($from, $prefix);
        } elseif (empty($from) && ! is_numeric($from) && ! empty($to)) {
            return static::prefix($to, $prefix);
        } elseif (empty($from) && ! is_numeric($from) && empty($to) && ! is_numeric($to)) {
            return 0;
        } else {
            $from = static::prefix($from, $prefix);
            $to = static::prefix($to, $prefix);
            return sprintf('%s%s%s', $from, $separator, $to);
        }
    }

    public static function prefix($value, $prefix)
    {
        return sprintf('%s%s', $prefix, $value);
    }

    /**
     * Format a number for user display
     *
     * @param $number
     * @param bool $isInteger
     * @param bool $alwaysNumber
     *
     * @return array|int|string|string[]|null
     */
    public static function formatNumberForUser($number, bool $isInteger = false, bool $alwaysNumber = true)
    {
        // TODO: Make this work with incorrect values (reverseFormat?)

        // Return null if value is not a useful number
        if (! $alwaysNumber && empty($number) && ! is_numeric($number)) {
            return null;
        }

        $number = static::format($number, ($isInteger ? 0 : 1));
        if ($isInteger) {
            // Remove thousand separator
            $number = str_replace(self::$formatLocaleSeparators[app()->getLocale()]['thousands'], '', $number);
        }

        // We don't want decimals on a 0
        if (Str::isConsideredEmptyAnswer($number)) {
            $number = 0;
        }

        return $number;
    }

    protected static function removeMultipleDecimals($number)
    {
        $locale = app()->getLocale();
        // check if multiple decimals were added to the input

        if ('en' != $locale) {
            // always for dot.
            $number = self::countAndRemoveDownToOne($number, '.');
        }

        $number = self::countAndRemoveDownToOne($number, self::$reverseLocaleSeparators[$locale]['decimal']);

        return $number;
    }

    /**
     * We use a for while on purpose. In theory one could also use preg_replace,
     * BUT: a dot is treated as a regex operator which is undesired.
     *
     * @param string $number
     * @param string $sign
     *
     * @return string
     */
    protected static function countAndRemoveDownToOne(string $number, string $sign): string
    {
        $decimalSignCount = substr_count($number, '.');

        $len = strlen($sign);
        while ($decimalSignCount-- > 1 && false !== ($pos = strpos($number, $sign))) {
            $number = substr_replace($number, '', $pos, $len);
        }

        return $number;
    }
}
