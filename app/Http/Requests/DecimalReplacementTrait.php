<?php

namespace App\Http\Requests;

use App\Helpers\Arr;
use App\Helpers\NumberFormatter;

trait DecimalReplacementTrait
{
    protected function decimals(array $keys)
    {
        $merges = [];
        foreach ($keys as $key) {
            $dec = $this->input($key);
            $dec = NumberFormatter::reverseFormat($dec);
            $merges = array_merge_recursive($merges, Arr::arrayUndot([$key => $dec]));
        }
        $this->replace(array_replace_recursive($this->all(), $merges));
    }
}
