<?php

namespace App\Services\Kengetallen\Resolvers;

class CodeDefined implements KengetallenDefiner
{
    public function get($kengetallenCode): float
    {
        return constant('Kengetallen::'.$kengetallenCode);
    }
}