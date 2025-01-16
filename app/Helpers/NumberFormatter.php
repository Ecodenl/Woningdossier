<?php

namespace App\Helpers;

class NumberFormatter
{
    /**
     * Separators for viewing (so, mostly used in the views or data for the view).
     *
     * @var array
     */
    protected static array $formatLocaleSeparators = [
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
    protected static array $reverseLocaleSeparators = [
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
     */
    public static function round(null|string|int|float $number, int $bucket = 5): int
    {
        $bucket = $bucket <= 0 ? 1 : $bucket;

        if (! is_numeric($number)) {
            $number = static::reverseFormat($number);
        }

        $result = round($number / $bucket) * $bucket;

        // If result is loosely 0, it means it could also be negative (e.g. -0.0). We force 0.
        if ($result == 0) {
            return 0;
        }

        return (int) $result;
    }

    /**
     * Used to format the given number in a human-readable format, mainly used for frontend display.
     */
    public static function format(null|string|int|float $number, int $decimals = 0, bool $shouldRoundNumber = false): string
    {
        $locale = app()->getLocale();
        if (is_null($number)) {
            $number = '0';
        }

        // if the number is numeric we can format it
        // else we return the value that's not a correct number
        if (is_numeric($number)) {
            if ($shouldRoundNumber) {
                $number = static::round($number);
            }

            return number_format(
                $number,
                $decimals,
                self::$formatLocaleSeparators[$locale]['decimal'],
                self::$formatLocaleSeparators[$locale]['thousands']
            );
        } else {
            return $number;
        }
    }

    public static function mathableFormat(string $number, int $decimals = 0): string
    {
        $number = str_replace(',', '.', $number);

        if (is_numeric($number)) {
            $number = number_format((float) $number, $decimals, '.', '');
        }

        return $number;
    }

    public static function reverseFormat(?string $number): float
    {
        $locale = app()->getLocale();
        if (empty($number)) {
            $number = 0;
        } else {
            $number = self::removeMultipleDecimals($number);
        }

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

        return (float) $result;
    }

    public static function range(null|string|int|float $from, null|string|int|float $to, int $decimals = 0, string $separator = ' - ', string $prefix = ''): string
    {
        $from = static::mathableFormat((string) $from, $decimals);
        $from = static::format($from, $decimals);

        $to = static::mathableFormat((string) $to, $decimals);
        $to = static::format($to, $decimals);

        if (! empty($from) && empty($to) && ! is_numeric($to)) {
            return static::prefix($from, $prefix);
        } elseif (empty($from) && ! is_numeric($from) && ! empty($to)) {
            return static::prefix($to, $prefix);
        } elseif (empty($from) && ! is_numeric($from) && empty($to) && ! is_numeric($to)) {
            return '0';
        } else {
            $from = static::prefix($from, $prefix);
            $to = static::prefix($to, $prefix);
            return sprintf('%s%s%s', $from, $separator, $to);
        }
    }

    public static function prefix(?string $value, ?string $prefix): string
    {
        return sprintf('%s%s', $prefix, $value);
    }

    /**
     * Format a number for user display
     */
    public static function formatNumberForUser(null|string|int|float $number, bool $isInteger = false, bool $alwaysNumber = true): ?string
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
            $number = '0';
        }

        return $number;
    }

    protected static function removeMultipleDecimals(string $number): string
    {
        $locale = app()->getLocale();
        // check if multiple decimals were added to the input

        if ('en' != $locale) {
            // always for dot.
            $number = self::countAndRemoveDownToOne($number, '.');
        }

        return self::countAndRemoveDownToOne($number, self::$reverseLocaleSeparators[$locale]['decimal']);
    }

    /**
     * We use a for while on purpose. In theory one could also use preg_replace,
     * BUT: a dot is treated as a regex operator which is undesired.
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
