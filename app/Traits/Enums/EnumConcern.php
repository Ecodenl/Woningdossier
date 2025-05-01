<?php

namespace App\Traits\Enums;

use Illuminate\Support\Collection;

trait EnumConcern
{
    public static function values(): Collection
    {
        return collect(self::cases())->pluck('value', 'name');
    }

    public static function fill(iterable $values): array
    {
        $result = [];

        foreach (self::cases() as $case) {
            $result[$case->value] = $values[$case->value] ?? [];
        }

        return $result;
    }
}
