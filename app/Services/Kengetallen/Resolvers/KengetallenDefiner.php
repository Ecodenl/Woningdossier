<?php

namespace App\Services\Kengetallen\Resolvers;

use App\Traits\Services\HasBuilding;

interface KengetallenDefiner
{
    public function get($kengetallenCode);
}