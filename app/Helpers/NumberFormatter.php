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
     * @param  int  $bucket
     *
     * @return float|int
     */
    public static function round($number, $bucket = 5)
    {
        if (!is_numeric($number)) {
            $number = static::reverseFormat($number);
        }
        return round($number / $bucket) * $bucket;
    }


    public static function format($number, $decimals = 0, $shouldRoundNumber = false)
    {
        $locale = app()->getLocale();
        if (is_null($number)) {
            $number = 0;
        }

        // if the number is numeric we can format it
        // else we return the value thats not a correct number
        if (is_numeric($number)) {

            if ($shouldRoundNumber) {
                $roundedNumber = static::round($number);
            }

            $formattedNumber = number_format(
                $roundedNumber ?? $number,
                $decimals,
                self::$formatLocaleSeparators[$locale]['decimal'],
                self::$formatLocaleSeparators[$locale]['thousands']
            );


            return $formattedNumber;

        } else {
            return $number;
        }
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

        return str_replace(self::$reverseLocaleSeparators[$locale]['decimal'],
            '.',
            $number);
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
    protected static function countAndRemoveDownToOne($number, $sign)
    {
        $decimalSignCount = substr_count($number, '.');

        $len = strlen($sign);
        while ($decimalSignCount-- > 1 && false !== ($pos = strpos($number, $sign))) {
            $number = substr_replace($number, '', $pos, $len);
        }

        return $number;
    }
}
