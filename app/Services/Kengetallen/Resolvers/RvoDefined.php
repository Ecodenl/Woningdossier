<?php

namespace App\Services\Kengetallen\Resolvers;

class RvoDefined extends KengetallenDefiner
{
    public function get($kengetallenCode): float
    {
        return constant('Kengetallen::'.$kengetallenCode);
    }
}