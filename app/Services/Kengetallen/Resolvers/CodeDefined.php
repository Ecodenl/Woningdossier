<?php

namespace App\Services\Kengetallen\Resolvers;

class CodeDefined extends KengetallenDefiner
{
    public function get($kengetallenCode): float
    {
        return constant('Kengetallen::'.$kengetallenCode);
    }
}