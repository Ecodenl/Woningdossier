<?php

namespace App\Services\Kengetallen\Resolvers;

class RvoDefined extends KengetallenDefiner
{
    public function get(string $kengetallenCode): float
    {
        return constant('Kengetallen::' . $kengetallenCode);
    }
}
